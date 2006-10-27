REDAME file for the clone module for Drupal 5.x.

The clone module allows users to make a copy of an existing node and then edit 
that copy. The authorship is set to the current user, the menu and url aliases 
are reset, and the words "Clone of" are inserted into the title to remind you 
that you are not editing the original node.

Users with the "clone node" permission can utilize this functionality. A new
tab will appear on node pages with the word "clone".  Once you click this
tab you have *already* created a new node that is a copy of the node you were
viewing, and you will be redirected to an edit screen for that new node.

This module makes reasonable checks on access permissions.  A user cannot clone 
a node unless they can use the input format of that node, and unless they have
permission to create new nodes of that type based on a call to node_access().

Settings can be accessed at admin/settings/clone.  On this page you can
set whether an additional confirmation screen is required before making a clone
of a node, and also set whether the publishing options are reset when making
a clone of a node.  This is set for each node type individually.

This module seems to work with common node types, however YMMV. File 
attachments are not included in the cloned node.  It seems to produce
functional clones of forms made using the webform module, but because of 
replication of some of the webforms IDs, the form's theming will always be
the same as the original webform. In all cases, but especially
if you are using a complex or custom node type, you should evaluate this
module on a test site with a copy of your database before attempting to use
it on a live site.

To install this module, copy it to the /modules directory of your Drupal 
installation and enable it at /admin/modules.  A new permission is created, but 
there are no changes to the database structure.

Note: this module originally derived from code posted by Steve Ringwood 
(nevets@drupal) at http://drupal.org/node/73381#comment-137714

