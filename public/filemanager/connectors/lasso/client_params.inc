[//lasso
	// create a single array of both get and post params
	define_tag(
		'params',
		-namespace='client_',
		-priority='replace',
		-description='Returns an array that is a combination of [client_getparams] and [client_postparams].'
	);
		local('out') = array;
		#out->merge(client_getparams);
		#out->merge(client_postparams);
		return(#out);
	/define_tag;

	define_tag(
		'param',
		-namespace='client_',
		-req='param',
		-opt='count',
		-opt='explode',
		-priority='replace',
		-description='Equivalent of [action_param], only for [client_params]. Safe to use inside inlines.'
	);
		local('out') = string;	
		local('key') = #param;		
		
		if(params >> '-count');
			#out = client_params->find(#key)->size;
			
		else(params >> '-explode');
			#out = array;
			local('matches') = client_params->find(#key);
			
			if(#matches->size > 1);
				iterate(#matches, local('i'));
					#i->isa('pair') ? #out->insert(#i->second) | #out->insert(#i);
				/iterate;
				
			else(#matches->size);
				#matches->first->isa('pair') ? #out->insert(#matches->first->second) | #out->insert(#matches->first);
				
			/if;
			
		else(params->size >= 2 && params->get(2)->isa('integer'));
			local('index') = params->get(2);
			
			if(client_params->find(#key)->size >= #index);
				#out = client_params->find(#key)->get(#index)->second;

			else;
				#out = '';
				
			/if;	
		else;
			local('matches') = client_params->find(#key);
			
			if(#matches->size > 1);
				iterate(#matches, local('i'));
					#out += (#i->isa('pair') ? (#i->second + '\r') | (#i + '\r'));
				/iterate;
				
			else(#matches->size);
				#out = (#matches->first->isa('pair') ? #matches->first->second | #matches->first);
				
			else;
				#out = '';
			/if;
			
		/if;
		
		return(#out);
	/define_tag;
]	
