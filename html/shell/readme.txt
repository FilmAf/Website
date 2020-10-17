#!/bin/bash

#p0/###    small                *.gif +++ already populated
#p1/###    medium               *.jpg --- to be populated
#p2/###    large                *.jpg -
#p3/###    huge                 *.jpg -
#p4/###    original display     *.jpg --- to be populated
#p5/###    edited               *.pgn
#p6/###    original             *.jpg +++
#uploads   unprocessed pics     *.jpg, *.gif, *.bmp, *.png

propagate-new [filename] [bo|nb|hd|bd|kr|kw|sc] transform instructions...
save-new [source] [target]
save-old [source] [version] [sub-version] [newpic]
try-transform [source] [target] [bo|nb|hd|bd|kr|kw|sc] transform instructions...


New Title:
#   if ( transforms )
#	try-transform "/var/www/html/uploads/name.[jpg,gif,png,bmp]" "/var/www/html/uploads/name-seed.jpg" [bo|nb|hd|bd|kr|kw|sc] transform instructions
#   if ( approved ) then
#	save title, reset picture number, reset picture version and reset sub-version
#       save-new "name.[jpg,gif,png,bmp]" "002000-d0"
#	propagate-new "002000-d#" [bo|nb|hd|bd|kr|kw|sc] transform instructions

Add New picture:
#   if ( transforms )
#	try-transform "/var/www/html/uploads/name.[jpg,gif,png,bmp]" "/var/www/html/uploads/name-seed.jpg" [bo|nb|hd|bd|kr|kw|sc] transform instructions
#   if ( approved ) then
#	increment picture number, reset picture version and reset sub-version
#       save-new "name.[jpg,gif,png,bmp]" "002000-d#"
#	propagate-new "002000-d#" [bo|nb|hd|bd|kr|kw|sc] transform instructions

Replace Existing picture:
#   if ( transforms )
#	try-transform "/var/www/html/uploads/name.[jpg,gif,png,bmp]" "/var/www/html/uploads/name-seed.jpg" [bo|nb|hd|bd|kr|kw|sc] transform instructions
#   if ( approved ) then
#	save-old "002000-d#" [version] [sub-version] "newpic"
#       increment picture version and reset sub-version
#       save-new "name.[jpg,gif,png,bmp]" "002000-d#"
#	propagate-new "002000-d#" [bo|nb|hd|bd|kr|kw|sc] transform instructions

Editing existing picture:
#   if ( transforms )
#	try-transform "/var/www/html/p6/000/002000-d0.jpg" "/var/www/html/uploads/user-seed.jpg" [bo|nb|hd|bd|kr|kw|sc] transform instructions
#   if ( approved ) then
#	save-old "002000-d#" [version] [sub-version]
#       increment sub-version
#	propagate-new "002000-d#" [bo|nb|hd|bd|kr|kw|sc] transform instructions

