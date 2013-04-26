#!/usr/bin/perl -w

# This handler requires JSON::XS.
use strict;
use warnings;
use CGI qw(:standard);
use utf8;

my $base_url = "http://localhost"; # The base URL for your file directory
my $root = "/srv/http/jwysiwyg"; # Local URI for file directory
my $action = param("action");

print "Content-type: text/plain; charset=utf-8\n\n";

my $dir = url_decode(param("dir"));
my $fhandle = param("handle");
my $newName = url_decode(param("newName"));

# List of allowed extensions:
my %allowed = (	".jpg" => 1, 
				".png" => 1, 
				".gif" => 1
				);

# Upload File Method:
if ($action eq "upload") {
	if ($fhandle) {
		if ($newName) {
			$newName =~ /(\..*)/;
			my $suffix = $1;
			if (exists $allowed{$1}) {
				$newName =~ /([\w\.\(\)_\-\s]+)/;
				$newName = $1;
				my $fbuffer;
				open(FH, "> $root$dir$newName") || ((warn "$!") && return 0);
				binmode FH;
				while (read($fhandle, $fbuffer, 2048)) {
					unless (print FH "$fbuffer") { 
						print "Something went wrong while uploading the file.";
						last;
					}
				}
				close (FH);
				print "$newName Has been uploaded.";
			} else {
				print "Only [.jpg, .png, .gif] formats are allowed.";
			}
		} else {
			print "Must give the file a name.";
		}
	} else {
		print "No file selected.";
	}
} else {
	print"File upload is not yet supported.";
}

# Method for decoding GET method utf8
sub url_decode {
	my $arg = $_[0];
	if ($arg =~ /\/?\.\.\/?/) { return 0; } # This is an important safety precaution -> Using firebug it is very easy to access all the data on the server, simply by using the hard-links of the linux system: ../ and ./ to reach higher level directories.
	# Support for Unicode, UTF-8:
	$arg =~ tr/+/ /;
	$arg =~ s/%([a-fA-F0-9]{2})/pack("C", hex($1))/eg;
	$arg =~ s/%u0([a-fA-F0-9]{3})/pack("U", hex($1))/eg;
	utf8::decode($arg);	
	return $arg;
}
