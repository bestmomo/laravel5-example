[//lasso
	// Initialization
	// ----------------------------------------------------

	/* live loading for debugging only
	library('encode_json.inc');
	library('client_params.inc');
	library('encode_urlpath.inc');
	library('dictionary.inc');
	library('filemanager.inc');
	// */

	// /*
	namespace_using(namespace_global);
		library('encode_json.inc'); // load every time to avoid version mismatches
		!lasso_tagexists('client_params') || !lasso_tagexists('client_param') ? library('client_params.inc');
		!lasso_tagexists('encode_urlpath') ? library('encode_urlpath.inc');
		!lasso_tagexists('dictionary') ? library('dictionary.inc');
		!lasso_tagexists('filemanager') ? library('filemanager.inc');	
	/namespace_using;
	// */

	protect;
		handle_error;
			log_critical('[Filemanager] Configuration file is missing or corrupt.');
			content_type('text/plain');
			content_body = '{Error: "Configuration File Missing", Code: -1}';
			abort;
		/handle_error;

		library('filemanager.config.inc');
	/protect;

	var('fm') = filemanager($config);



	// Authorization
	// ----------------------------------------------------

	if(!validuser);
		log_critical('[FileManager] Not a valid user.');
		content_type('text/plain');
		content_body = '{Error: "' + $fm->lang->find('AUTHORIZATION_REQUIRED') + '", Code: ' + error_nopermission( -errorcode) + '}';
		abort;
	/if;



	// Request Handling
	// ----------------------------------------------------

	inline($fm->config->auth);			
		select(client_param('mode'));
			case('getinfo');
				content_type('text/plain');
				content_body = $fm->getinfo(
					-path=client_param('path'), 
					-getsize=(client_param('getsize') != 'false')
				);
				abort;
	
			case('getfolder');
				content_type('text/plain');
				content_body = $fm->getfolder(
					client_param('path'), 
					-getsizes=boolean(client_param('showThumbs')),
					-type=client_param('type')
				);
				abort;
			
			case('rename');
				content_type('text/plain');
				content_body = $fm->rename(
					client_param('old'), 
					client_param('new')
				);
				abort;		
			
			case('delete');
				content_type('text/plain');
				content_body = $fm->delete(client_param('path'));
				abort;		
			
			case('add');
				content_type('text/html');
				content_body = $fm->add(client_param('currentpath'));
				abort;		
			
			case('addfolder');
				content_type('text/plain');
				content_body = $fm->addfolder(
					client_param('path'), 
					client_param('name')
				);
				abort;		
			
			case('download');
				$fm->download(client_param('path'));
				
			case;
				content_type('text/plain');
				content_body = encode_json(map(
					'Error' = $fm->getlang('MODE_ERROR'),
					'Code' = -1
				));
				abort;
				
		/select;	
	/inline;
]
