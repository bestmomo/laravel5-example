[//lasso
	// function to check for a valid user -- change to something real!
	define_tag('validuser');
		return(true);
	/define_tag;
	
	var('config') = dictionary(
		'auth' = array( // set to a Lasso user with file tags permissions
			-username='',
			-password=''		
		),
		'culture' = 'en', // language
		'dateformat' = '%D %r', // date format
		'icons' = dictionary(
			'path' = 'images/fileicons/', // path to icons
			'directory' = '_Open.png', // directory icon
			'default' = 'default.png' // default/generic icon
		),
		'upload' = dictionary(
			'overwrite' = true, // allow uploads to overwrite existing files
			'size' = false, // integer or false - max upload size in bytes
			'imagesonly' = false, // only allow images to be uploaded
			'exclude' = set('cfm','cfml','cfc','dbm','jsp','asp','aspx','exe','php','cgi','shtml','rb','msi','lasso','lassoapp','inc','py','pyc') // file types that can't be uploaded
		),
		'fileroot' = '/UserFiles/', // location of files
		'imageexts' = set('gif','jpg','jpeg','png'), // valid image extensions
		'exclude' = set('_thumbs') // files/folders to exclude from listing (all files beginning with a period, also)
	);
]
