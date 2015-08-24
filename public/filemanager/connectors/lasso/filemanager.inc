[//lasso
	define_type(
		'filemanager',
		-prototype,
		-description='Web-based filemanager for use with inline HTML editors.'
	);
		// Setup
		// ------------------------------------------------

		local('config','lang');
	
		
		
		define_tag(
			'oncreate', 
			-req='config', -type='dictionary'
		);
			self->config = #config;
			self->setlang;
		/define_tag;

		
		
		define_tag('setlang'); // TODO: verify this will work with relative paths
			local('langfile') = '../../scripts/languages/' + self->config->culture + '.js';
			!file_exists(#langfile) ? #langfile = '../../scripts/languages/en.js';
			self->lang = decode_json(string(include_raw(#langfile)));
			error_code ? log_critical('[FileManager] Error loading language file: ' + error_msg);
		/define_tag;

		


		// Utility Functions
		// ------------------------------------------------
		
		define_tag(
			'getlang',
			-req='str',
			-opt='ins', -type='array',
			-description='Returns the localized error message.'
		);
			local('msg') = self->lang->find(#str);
			!local_defined('ins') || !#ins->size ? return(#msg);		
			local('re') = regexp( -input=#msg, -find='%s');
			
			iterate(#ins, local('i'));
				#re->replacefirst( -replace=#i);
			/iterate;
			
			return(#re->output);
		/define_tag;

		
		
		define_tag(
			'isvalidpath',
			-req='path',
			-description='Returns an error if the given path is not within the specified root path.'
		);			
			return(#path->beginswith(self->config->fileroot));
		/define_tag;
	
		
		
		define_tag(
			'patherror',
			-description='Returns the localized error message for invalid paths.'
		);
			return(map(
				'Error' = self->getlang('INVALID_DIRECTORY_OR_FILE'),
				'Code' = error_nopermission( -errorcode)
			));
		/define_tag;
	
		



		// "Public" Methods
		// ------------------------------------------------
		
		define_tag(
			'getinfo',
			-req='path',
			-opt='getsize', -type='boolean',
			-description='Returns a JSON object containing information about the given file.'
		);
			!self->isvalidpath(#path) ? return(self->patherror);

			local('file') = map(
				'Filename' = string(#path)->removetrailing('/')&split('/')->last,
				'File Type' = 'txt',
				'Preview' = self->config->icons->path + self->config->icons->default,
				'Path' = #path,
				'Error' = '',
				'Code' = 0,
				'Properties' = map(
					'Date Created' = '',
					'Date Modified' = '',
					'Width' = '',
					'Height' = '',
					'Size' = ''
				)
			);
			
			!local_defined('getsize') ? local('getsize') = true;
		
			if(!file_exists(#path));
				#file->find('Error') = self->getlang('FILE_DOES_NOT_EXIST', array(#path));
				#file->find('Code') = file_currenterror( -errorcode);
				return(encode_json(#file));
			/if;
			
			if(#path->endswith('/'));
				#file->find('File Type') = 'dir';
				#file->find('Preview') = self->config->icons->path + self->config->icons->directory;
			else;
				local('ext') = #path->split('.')->last;
				#file->find('File Type') = #ext;
				
				if(self->config->imageexts >> #ext && #getsize);
					local('img') = image(#path);
					#file->find('Properties')->find('Width') = #img->width;
					#file->find('Properties')->find('Height') = #img->height;
					#file->find('Preview') = #path;
				else;
					local('previewPath') = self->config->icons->path + #ext->uppercase& + '.png';
					#file->find('Preview') = (file_exists('../../' + #previewPath) ?  #previewPath | self->config->icons->path + self->config->icons->default);
				/if;
			/if;

			protect;
				#file->find('Properties')->find('Date Created') = file_creationdate(#path)->format(self->config->dateformat);
			/protect;
			
			protect;
				#file->find('Properties')->find('Date Modified') = file_moddate(#path)->format(self->config->dateformat);
			/protect;
			
			local('rawsize') = integer(file_getsize(#path));
			#file->find('Properties')->find('Size') = #rawsize;
			
			return(encode_json(#file));
		/define_tag;

		
		
		define_tag(
			'getfolder',
			-req='path',
			-opt='getsizes', -type='boolean',
			-opt='type'
		);
			!self->isvalidpath(#path) ? return(self->patherror);
			!local_defined('getsizes') ? local('getsizes') = true;

			!file_exists(#path) ? return(encode_json(map(
				'Error' = self->getlang('DIRECTORY_NOT_EXIST', array(#path)), 
				'Code' = -1
			)));

			local(
				'out' = array,
				'raw' = file_listdirectory(#path)->sort&,
				'dirs' = array,
				'files' = array,
				'valid' = null
			);
			
			if(local_defined('type') && #type == 'images');
				iterate(#raw, local('i'));
					#i->beginswith('.') || #i->endswith('/') || self->config->imageexts !>> #i->split('.')->last ? loop_continue;
					#valid->insert(#i);					
				/iterate;			
			else;
				// sort directories ahead of files
				iterate(#raw, local('i'));
					#i->beginswith('.') ? loop_continue;
					local('p') = #i;
					#p->endswith('/') ? #dirs->insert(#p) | #files->insert(#p);
				/iterate;
				
				#valid = #dirs->merge(#files)&;
			/if;
			
			iterate(#valid, local('i'));
				self->config->exclude !>> #i ? #out->insert(literal(self->getinfo(#path + #i, -getsize=#getsizes)));
			/iterate;
			
			return(encode_json(#out));
		/define_tag;
		
		
		
		define_tag(
			'rename',
			-req='old',
			-req='new'
		);
			!self->isvalidpath(#old) ? return(self->patherror);

			local('oldname') = #old->removetrailing('/')&split('/')->last;
			local('path') = string(#old)->removetrailing(#oldname)&;
			!#path->endswith('/') ? #path->append('/');
			local('newname') = encode_urlpath(#new);
			local('newpath') = #path + #newname;
			local('isdir') = #newpath->endswith('/');
			
			if(file_exists(#newpath));
				local('error') = self->getlang(
					(#isdir ? 'DIRECTORY_ALREADY_EXISTS' | 'FILE_ALREADY_EXISTS'),
					array(#newpath)
				);
				
				local('code') = -1;
			else;
				file_move(#old, #newpath, -fileoverwrite);
				
				if(file_currenterror( -errorcode));
					local(
						'error' = self->getlang(
							(#isdir ? 'ERROR_RENAMING_DIRECTORY' | 'ERROR_RENAMING_FILE'),
							array(#newpath)
						),
						'code' = file_currenterror( -errorcode)
					);
				else;
					local(
						'error' = self->getlang('successful_rename'),
						'code' = 0
					);			
				/if;
			/if;
						
			local('result') = map(
				'Old Path' = #old,
				'Old Name' = #oldname,
				'New Path' = #newpath,
				'New Name' = #newname,
				'Code' = #code, 
				'Error' = #error
			);
			
			return(encode_json(#result));
		/define_tag;

		
		
		define_tag(
			'delete',
			-req='path'
		);
			!self->isvalidpath(#path) ? return(self->patherror);

			file_delete(#path);
			
			local('result') = map(
				'Path' = #path,
				'Error' = file_currenterror,
				'Code' = file_currenterror( -errorcode)
			);
			
			return(encode_json(#result));
		/define_tag;
		
		
		
		define_tag(
			'add',
			-req='path',
			-encodenone
		);		
			!self->isvalidpath(#path) ? return(self->patherror);

			if(file_uploads->size);
				local('upload') = file_uploads->first;
				local('ext') = #upload->find('origname')->split('.')->last;
				local('size') = #upload->find('size');
				local('newName') = encode_urlpath(#upload->find('origname'));
				local('newPath') = #path + #newName;
				
				if(file_exists(#newPath) && !self->config->upload->overwrite);
					local(
						'error' = self->getlang('FILE_ALREADY_EXISTS'),
						'code' = -1
					);
				else(self->config->upload->imagesonly && self->config->imageexts !>> #ext);
					local(
						'error' = self->getlang('UPLOAD_IMAGES_ONLY'),
						'code' = -1
					);
				else(self->config->upload->exclude >> #ext);
					local(
						'error' = self->getlang('INVALID_FILE_UPLOAD'),
						'code' = -1
					);
				else(self->config->upload->maxsize && self->config->upload->maxsize < #size);
					local(
						'error' = self->getlang('UPLOAD_FILES_SMALLER_THAN', array(self->config->upload->maxsize)),
						'code' = -1
					);				
				else;
					file_copy(#upload->find('path'), #path + #newName);
					local(
						'error' = file_currenterror,
						'code' = file_currenterror( -errorcode)
					);
				/if;

				local('result') = map(
					'Path' = #path,
					'Name' = #newName,
					'Error' = #error,
					'Code' = #code
				);
			else;
				local('result') = map(
					'Path' = #path,
					'Error' = self->getlang('INVALID_FILE_UPLOAD'),
					'Code' = -1
				);		
			/if;
			
			return(@('<textarea>' + encode_json(#result) + '</textarea>'));
		/define_tag;
		
		
		
		define_tag(
			'addfolder',
			-req='path',
			-req='name'
		);
			!self->isvalidpath(#path) ? return(self->patherror);

			local('newName') = encode_urlpath(#name);
			local('newPath') = #path + #newName + '/';
			
			if(file_exists(#newPath));
				local(
					'error' = self->getlang('DIRECTORY_ALREADY_EXISTS'),
					'code' = -1
				);
			else;
				file_create(#newPath);
				local(
					'error' = (file_currenterror( -errorcode) ? self->getlang('UNABLE_TO_CREATE_DIRECTORY', array(#newPath)) | file_currenterror),
					'code' = file_currenterror( -errorcode)
				);
			/if;
			
			local('result') = map(
				'Parent' = #path,
				'Name' = #newName,
				'Error' = #error,
				'Code' = #code
			);
			
			return(encode_json(#result));			
		/define_tag;
		
		
		
		define_tag(
			'download',
			-req='path'
		);
			!self->isvalidpath(#path) ? return(self->patherror);
			local('name') = #path->split('/')->last;
			local('file') = include_raw(#path);
			file_serve(#file, #name, -type='application/x-download');
		/define_tag;
	/define_type;
]
