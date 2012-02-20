#!/usr/bin/env perl

# $data format is a list of semicolon-separated "lines".
# Each line is a comma-separated list of color/repeat values that defines
# one row of pixels in the image.
# Each value is a hex color, an underscore, and a decimal repeat value.
# If the hex color is <6 digits, pad it on the left with zeroes

# Example: 0_10,ff_15,ff00ff_5;1_5,f0_20,ff_5
# This defines 2 rows of pixels. The first row has 10 pixels of color #000000,
# followed by 15 pixels of color #0000ff, followed by 5 pixels of #ff00ff.
# Second row is 5 pixels of color #000001, then 20 pixels of #0000f0, then
# 5 pixels of #0000ff.

use warnings;
use strict;

use CGI qw(:standard);
use Image::Magick;

# To display error messages in the browser, uncomment the following line.
# Without it, the user sees "500 Internal Server Error" in case of error.
#use CGI::Carp qw(fatalsToBrowser);

# Extract parameters
my $width   = param('width')   || 0;
my $height  = param('height')  || 0;
my $bgcolor = param('bgcolor') || "#ffffff";
my $data    = param('data')    || die "Missing 'data' parameter\n";

# Check parameters
unless(($width > 0) and ($height > 0)) {
	die("Width and height of chart missing or invalid.\n");
}

# Image::Magick wants a # in front of the color... also pad left with zeroes
$bgcolor = sprintf("#%06x", hex($1)) if $bgcolor =~ /^#?([0-9a-f]{1,6})$/i;

# Parameters are OK, create blank image
my $chart = new Image::Magick(size => $width . "x" . $height);
$chart->ReadImage("xc:$bgcolor");
$chart->Set('quality', 100);

# Parse the data and plot pixels
my @rows = split /;/, $data;

my $vert_pos = 0;
for(@rows) {
	my $horiz_pos = 0;

	my @pixels = split /,/;
	for(@pixels) {
		my ($color, $repeat) = split /_/;

		if($color ne "") { # there's a color, plot a $repeat wide block of pixels
			$color = sprintf("#%06x", hex($color)); # pad left with zeroes

			for(my $x = 0; $x < $repeat; $x++) { # plot each pixel
				$chart->Set("pixel[$horiz_pos,$vert_pos]" => $color);
				$horiz_pos++;
			}
		} else { # no color, just skip $repeat wide block of pixels
			$horiz_pos += $repeat;
		}
	}

	$vert_pos++;
}

# Parsing/plotting done, emit result to browser. The content_disposition
# forces a download dialog, instead of having the browser display the
# image.
print header(
		-type => 'image/jpeg',
		-content_disposition => 'attachment; filename="FusionCharts.jpg"');

binmode STDOUT; # Not really needed on *NIX
$chart->Write("jpg:-");

undef $chart; # Only need this if running via mod_perl

exit 0;

