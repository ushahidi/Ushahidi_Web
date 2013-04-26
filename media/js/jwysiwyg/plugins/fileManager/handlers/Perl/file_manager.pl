#!/usr/bin/perl -w

# This handler requires JSON::XS.
use strict;
use warnings;
use CGI qw(:standard);
use JSON::XS;
use utf8;

my $base_url = "http://localhost"; # The base URL for your file directory
my $root = "/srv/http/jwysiwyg"; # Local URI for file directory
my $action = param("action");

# All responses are strict JSON.
print "Content-type: application/json; charset=utf-8\n\n";

my $dir;
if (param("dir")) {
	$dir = param("dir");
	$dir = url_decode($dir);
}
my $JSON = JSON::XS->new()->pretty(1);


# Authentication Method:
if ($action eq "auth") {
	if (param("auth") eq "jwysiwyg") {
		print $JSON->encode ({	"success" => JSON::XS::true,
							"data" => {
								"move" => 	{	"handler" => "$base_url/cgi-bin/move_handler.pl",
												"enabled" => JSON::XS::true,
											},
								"rename" => {	"handler" => "$base_url/cgi-bin/rename_handler.pl",
												"enabled" => JSON::XS::true,
											},
								"remove" => {	"handler" => "$base_url/cgi-bin/remove_handler.pl",
												"enabled" => JSON::XS::true,
											},
								"mkdir" => {	"handler" => "$base_url/cgi-bin/mkdir_handler.pl",
												"enabled" => JSON::XS::true,
											},
								"upload" => {	"handler" => "$base_url/cgi-bin/upload_handler.pl",
												"enabled" => JSON::XS::true,
												"accept_ext" => [".png", ".jpg", ".gif"]
											}
								}
							});
	}
# List Directory Method:
} elsif ($action eq "list") {
	unless ($dir) { die "No directory specified."; }
	unless ($dir =~ m{/$}) { $dir = "$dir/"; }
	unless (-e "$root$dir") { warn "$dir: Directory does not exist."; exit; }
	opendir(DIR, "$root$dir") || die "Can't open $dir: $!";
	my $json = {	"directories" => {},
					"files" => {}
				};
	foreach (readdir(DIR)) {
		next if (($_ eq '.') || ($_ eq '..'));
		utf8::decode($_);
		if (-d "$root$dir$_") {
			$json->{"directories"}->{"$_"} = "$dir$_";
		} else {
			$json->{"files"}->{"$_"} = "$base_url$dir$_";
		}
	}
	closedir(DIR);
	print $JSON->encode({ "success" => JSON::XS::true, "data" => $json });	
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
