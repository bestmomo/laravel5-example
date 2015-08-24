#!/usr/bin/env perl
use CGI;
use JSON;
use Image::Info qw( image_info image_type);
use File::Basename;
use File::Find::Rule;
use File::Slurp;
use strict;

our $q;

#Edit this with your values in
require 'filemanager_config.pl';
my $config = $Filemanager::Config::config;
my $config_js = from_json(read_file( '../../scripts/filemanager.config.js', binmode => ':utf8' ), {utf8 => 1}) ;

my $MODE_MAPPING = {
  '' => \&root,
  getinfo => \&getinfo,
  getfolder => \&getfolder,
  rename => \&rename,
  delete => \&delete,
  addfolder => \&addfolder,
  add => \&add,
  download => \&download
};

sub main {
  $q = CGI->new;
  my $method = $MODE_MAPPING->{$q->param('mode')} || \&root;

  unless($q->param('mode') eq "download" || $q->param('mode') eq "add") {
    print $q->header('application/json') ;
  }
  &$method;
}

#?mode=getinfo&path=/UserFiles/Image/logo.png&getsize=true
#For now return image size info anyway
sub getinfo {
  return unless params_valid([qw(path)]);

  my $filename = relative_file_name_from_url($q->param('path'));

  print_json(file_info($filename));
}

sub file_info {
  my $rel_filename = shift;
  my $abs_filename = absolute_file_name_from_url($rel_filename);
  my $url_filename = url_for_relative_filename($rel_filename);

  my $info = image_info($abs_filename);
  my ($fileparse_filename, $fileparse_dirs, $fileparse_suffix) = fileparse($abs_filename);
  $fileparse_filename =~ /\.(.+)/;
  my $suffix = lc($1) || "";

  my $directory = -d $abs_filename;
  if($directory) {
    $url_filename .= "/";
  }

  my $preview = $config_js->{icons}{path}.(($directory)?$config_js->{icons}{directory}:$config_js->{icons}{default}); 
  if (grep{$suffix eq $_}@{$config_js->{images}->{imagesExt}}){
    $preview = $url_filename;
  } elsif (-e '../../'.$config_js->{icons}{path}.$suffix.'.png'){
    $preview = $config_js->{icons}{path}.$suffix.'.png';
  };
  return {
    "Path" => $url_filename,
    "Filename" => $fileparse_filename,
    "File Type" => $directory ? "dir" : $suffix,
    "Preview" => $preview,
    "Properties" => {
      "Date Created" => '', #TODO
      "Date Modified" => '', #"02/09/2007 14:01:06", 
      "Height" => $info->{height},
      "Width" => $info->{width},
      "Size" => -s $abs_filename 
    },
    "Error" => "",
    "Code" => 0
  }
}

# ?mode=getfolder&path=/UserFiles/Image/&getsizes=true&type=images
#Ignoring type for now
sub getfolder {
  return unless params_valid([qw(path)]);

  my @directory_list = ();

  my $rel_directory = relative_file_name_from_url($q->param('path'));
  my $directory = absolute_file_name_from_relative($rel_directory);

  my @directories = File::Find::Rule->maxdepth(1)->directory->in( $directory );
  my @files = File::Find::Rule->maxdepth(1)->file->in( $directory );

  foreach my $dir (@directories) {
    my $url_filename = url_for_relative_filename(relative_file_name_from_absolute($dir));
    #Skip current directory
    next if relative_file_name_from_absolute($dir) eq $rel_directory;

    # push(@directory_list, { $url_filename => file_info(relative_file_name_from_absolute($dir)) });
    push(@directory_list, file_info(relative_file_name_from_absolute($dir)));
  }

  foreach my $file (@files) {
    my $url_filename = url_for_relative_filename(relative_file_name_from_absolute($file));
    # push(@directory_list, { $url_filename => file_info(relative_file_name_from_absolute($file)) });
    push(@directory_list, file_info(relative_file_name_from_absolute($file)) );
  }

  print_json(\@directory_list);
}

# ?mode=rename&old=/UserFiles/Image/logo.png&new=id.png
sub rename {
  return unless params_valid([qw(old new)]);
  my $path = '';
  my $old_name = '';
  my $error = 0;
  my $full_old = absolute_file_name_from_url($q->param('old'));
  ($path, $old_name) = ($1, $2) if $full_old =~ m|^ ( (?: .* / (?: \.\.?\z )? )? ) ([^/]*) |xs;
  my $new_name = $q->param('new');
  $new_name =~ s|^ .* / (?: \.\.?\z )? ||xs;
  $error = 1 if $new_name =~ /^\.?\.?\z/;
  my $full_new = remove_extra_slashes("$path/$new_name");

  $error ||= (rename($full_old, $full_new))?0:1;

  print_json({
    "Error" => $error ? "Could not rename" : "No error",
    "Code" => $error,
    "Old Path" => url_for_relative_filename(relative_file_name_from_absolute($full_old)),
    "Old Name" => $old_name,
    "New Path" => url_for_relative_filename(relative_file_name_from_absolute($full_new)),
    "New Name" => $new_name
  });
}

#?mode=delete&path=/UserFiles/Image/logo.png
sub delete {
  return unless params_valid([qw(path)]);
  my $full_old = absolute_file_name_from_url($q->param('path'));
  my $success;
  if(-d $full_old) {
    $success = rmdir $full_old; 
  } else {
    $success = unlink $full_old;
  }  

  print_json({
    "Error" => $success ? "No error" : "Could not delete",
    "Code" => !$success,
    "Path" => $q->param('path')
  }); 
}

#Assuming this is the upload action? Documentation isn't much help
sub add {
  return unless params_valid([qw(currentpath newfile)]);

  my $path = $q->param('currentpath');
  my $abs_path = absolute_file_name_from_url($path);
  my $success = 0;

  my $lightweight_fh  = $q->upload('newfile');
  my $filename = $q->param('newfile');
  my $abs_filename = $abs_path . "/" . $filename ;
  $filename = relative_file_name_from_absolute($abs_filename);

  my $buffer;
  # undef may be returned if it's not a valid file handle
  if (defined $lightweight_fh) {
    # Upgrade the handle to one compatible with IO::Handle:
    my $io_handle = $lightweight_fh->handle;
    open (OUTFILE,'>>',$abs_filename);
    while (my $bytesread = $io_handle->read($buffer,1024)) {
      print OUTFILE $buffer;
    }
    $success = 1;
  }

  print $q->header('text/html');
  print "<textarea>";
  print_json({
    Path => $path,
    Name => $filename,
    Error => $success ? "No error" : "Could not upload",
    Code => !$success

  });
  print "</textarea>";

}

#Nice confusing path name for a folder!
# ?mode=addfolder&path=/UserFiles/&name=new%20logo.png
sub addfolder {
  return unless params_valid([qw(path name)]);

  my $path = $q->param('path');
  my $name = $q->param('name');
  my $full_path = absolute_file_name_from_url($path);
  my $new_name = relative_file_name_from_url($name); #We don't really need to cast to absolute path, but this gives us '..' security for free

  my $success = mkdir $full_path . "/" . $new_name;

  print_json({
    "Parent" => $path,
    "Name" => $new_name,
    "Error" => $success ? "No error" : "Could not add that folder", 
    "Code" => !$success
  });
}

# ?mode=download&path=/UserFiles/new%20logo.png
sub download {
  return unless params_valid(["path"]);

  my $full_path = absolute_file_name_from_url($q->param('path'));
  my $rel_path = relative_file_name_from_url($q->param('path'));
  my $info = file_info($rel_path);

  # print $q->redirect($q->param('path')); #Would be easier to just redirect

  print $q->header(-type => 'application/x-download', -attachment => $info->{Filename});

  open(DLFILE, "<$full_path") || error("couldn't open the file for sending");     
  my @fileholder = <DLFILE>;     
  close (DLFILE);     

  print @fileholder;     

}

sub relative_file_name_from_url {
  my $file = shift;
  if($file =~ /\.\./g) {
    error("Invalid file path");
    return undef;
  } 
  $file =~ s/$config->{url_path}//;
  return remove_extra_slashes($file);
}

sub relative_file_name_from_absolute {
  my $file = shift;
  $file =~ s/$config->{uploads_directory}//;
  return remove_extra_slashes($file);  
}

sub absolute_file_name_from_url {
  my $file_path = shift;

  if($file_path =~ /\.\./g) {
    error("Invalid file path");
    return undef;
  } 
  my $filename =  $config->{uploads_directory} . '/' . relative_file_name_from_url($file_path);
  return remove_extra_slashes($filename);
}

sub absolute_file_name_from_relative {
  my $filename = $config->{uploads_directory} . "/" . shift;
  return remove_extra_slashes($filename);  
}

sub url_for_relative_filename {
  my $filename = shift;
  my $url = $config->{url_path} . '/' .$filename;
  return remove_extra_slashes($url);
}

sub remove_extra_slashes {
  my $filename = shift;
  $filename =~ s/\/\//\//g;
  #Strip ending slash too
  $filename =~ s/\/$//g;
  return $filename;  
}

sub params_valid {
  my $params = shift;

  foreach my $param(@$params) {
    unless($q->param($param)) {
      error("$param missing");
      return undef;
    };
  }

  return 1;
}

#return json error
sub root {
  error("Mode not specified");
}

sub error {
  my $error = shift;
  print_json ({
    "Error" => $error,
    "Code" =>  -1    
  });
  $q->end_html;  
  die "Couldn't carry on";
}

sub print_json {
  my $hash = shift;

  my $json = JSON->new->convert_blessed->allow_blessed;

  print $json->encode($hash);
}

main();
