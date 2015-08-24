# -*- coding: utf-8 -*-
from __future__ import with_statement # This isn't required in Python 2.6     
__metaclass__ = type


from contextlib import closing, contextmanager 
import os, sys, traceback
import os.path

from mod_python import apache, util
from util import parse_qs

today = date.today

ver = sys.version_info
if ver[0]<2 and ver[1]<5:
    raise EnvironmentError('Must have Python version 2.5 or higher.')


try:
    import json
except ImportError:
    raise EnvironmentError('Must have the json module.  (It is included in Python 2.6 or can be installed on version 2.5.)')


try:
    from PIL import Image
except ImportError:
    raise EnvironmentError('Must have the PIL (Python Imaging Library).')
    

path_exists = os.path.exists
normalize_path = os.path.normpath
absolute_path = os.path.abspath 
make_url = urlparse.urljoin
split_path = os.path.split
split_ext = os.path.splitext


euncode_urlpath = urllib.quote_plus

encode_json = json.JSONEcoder().encode


def encodeURLsafeBase64(data):
    return base64.urlsafe_b64encode(data).replace('=','').replace(r'\x0A','')
       
def image(*args):
    raise NotImplementedError 



class Filemanager:

    """Replacement for FCKEditor's built-in file manager."""
    
    def __init__(self, fileroot= '/'):
        self.fileroot = fileroot
        self.patherror = encode_json(
                {
                    'Error' : 'No permission to operate on specified path.',
                    'Code' : -1
                }
            )

    def isvalidrequest(self, **kwargs):
        """Returns an error if the given path is not within the specified root path."""
                
        assert split_path(kwargs['path'])[0]==self.fileroot
        assert not kwargs['req'] is None
        


    def getinfo(self, path=None, getsize=true, req=None):
        """Returns a JSON object containing information about the given file."""

        if not self.isvalidrequest(path,req):
            return (self.patherror, None, 'application/json')

        thefile = {
            'Filename' : split_path(path)[-1],
            'File Type' : '',
            'Preview' : path if split_path(path)[-1] else 'images/fileicons/_Open.png',
            'Path' : path,
            'Error' : '',
            'Code' : 0,
            'Properties' : {
                    'Date Created' : '',
                    'Date Modified' : '',
                    'Width' : '',
                    'Height' : '',
                    'Size' : ''
                }
            }
            
        imagetypes = set('gif','jpg','jpeg','png')
        
    
        if not path_exists(path):
            thefile['Error'] = 'File does not exist.'
            return (encode_json(thefile), None, 'application/json')
        
        
        if split_path(path)[-1]=='/':
            thefile['File Type'] = 'Directory'
        else:
            thefile['File Type'] = split_ext(path)
            
            if ext in imagetypes:
                img = Image(path).size()
                thefile['Properties']['Width'] = img[0]
                thefile['Properties']['Height'] = img[1]
                
            else:
                previewPath = 'images/fileicons/' + ext.upper + '.png'
                thefile['Preview'] = previewPath if path_exists('../../' + previewPath) else 'images/fileicons/default.png'
        
        thefile['Properties']['Date Created'] = os.path.getctime(path) 
        thefile['Properties']['Date Modified'] = os.path.getmtime(path) 
        thefile['Properties']['Size'] = os.path.getsize(path)

        req.content_type('application/json')
        req.write(encode_json(thefile))


    def getfolder(self, path=None, getsizes=true, req=None):
    
        if not self.isvalidrequest(path,req):
            return (self.patherror, None, 'application/json')

        result = []         
        filtlist = file_listdirectory(path)

        for i in filelist:
             if i[0]=='.':
                result += literal(self.getinfo(path + i, getsize=getsizes))

        req.content_type('application/json')
        req.write(encode_json(result))
    
    
    def rename(self, old=None, new=None, req=None):
                
        if not self.isvalidrequest(path=new,req=req):
            return (self.patherror, None, 'application/json')
        
        if old[-1]=='/':
            old=old[:-1]
            
        oldname = split_path(path)[-1]
        path = string(old)
        path = split_path(path)[0]
        
        if not path[-1]=='/':
            path += '/'
        
        newname = encode_urlpath(new)
        newpath = path + newname
        
        os.path.rename(old, newpath)
        
        result = {
            'Old Path' : old,
            'Old Name' : oldname,
            'New Path' : newpath,
            'New Name' : newname,
            'Error' : 'There was an error renaming the file.' # todo: get the actual error
        }
        
        req.content_type('application/json')
        req.write(encode_json(result))
    

    def delete(self, path=None, req=None):
    
        if not self.isvalidrequest(path,req):
            return (self.patherror, None, 'application/json')

        os.path.remove(path)
        
        result = {
            'Path' : path,
            'Error' : 'There was an error renaming the file.' # todo: get the actual error
        }
        
        req.content_type('application/json')
        req.write(encode_json(result))
    
    
    def add(self, path=None, req=None):     

        if not self.isvalidrequest(path,req):
            return (self.patherror, None, 'application/json')
        
    
        try:
            thefile = util.FieldStorage(req)['file'] #TODO get the correct param name for the field holding the file            
            newName = thefile.filename
            
            with open(newName, 'rb') as f:
                            f.write(thefile.value) 
            
        except:

            result = {
                'Path' : path,
                'Name' : newName,
                'Error' : file_currenterror
            }
            
        else:
            result = {
                'Path' : path,
                'Name' : newName,
                'Error' : 'No file was uploaded.'
            }
    
        req.content_type('text/html')
        req.write(('<textarea>' + encode_json(result) + '</textarea>'))
        
    
    def addfolder(self, path, name):        

        if not self.isvalidrequest(path,req):
            return (self.patherror, None, 'application/json')

        newName = encode_urlpath(name)
        newPath = path + newName + '/'
        
        if not path_exists(newPath):
            try:
                os.mkdir(newPath)
            except:
            
                result = {
                    'Path' : path,
                    'Name' : newName,
                    'Error' : 'There was an error creating the directory.' # TODO grab the actual traceback.
                }
            
    
    def download(self, path=None, req=None):
    
        if not self.isvalidrequest(path,req):
            return (self.patherror, None, 'application/json')
            
        name = path.split('/')[-1]
                  
        req.content_type('application/x-download')
        req.filename=name
        req.sendfile(path)

    



myFilemanager = Filemanager(fileroot='/var/www/html/dev/fmtest/UserFiles/') #modify fileroot as a needed


def handler(req): 
    #req.content_type = 'text/plain' 
    #req.write("Hello World!") 

    if req.method == 'POST':
        kwargs = parse_qs(req.read())
    elif req.method == 'GET': 
        kwargs = parse_qs(req.args)
    
    #oldid = os.getuid()
    #os.setuid(501)

    try:
        method=str(kwargs['mode'][0])
        methodKWargs=kwargs.remove('mode')
        methodKWargs['req']=req
        
        myFilemanager.__dict__['method'](**methodKWargs)
        
        return apache.OK 


    except KeyError:
        return apache.HTTP_BAD_REQUEST   

    except Exception, (errno, strerror):
        apache.log_error(strerror, apache.APLOG_CRIT)
        return apache.HTTP_INTERNAL_SERVER_ERROR

    #os.setuid(oldid)

