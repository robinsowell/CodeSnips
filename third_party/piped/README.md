{exp:piped sql="Select member_id FROM exp_members WHERE group_id = 7"}
Will return a pipe delimited list of the results row. So:
1|2|3|6
Note- if you pull back more than one field? It just mushes the results together and is pretty useless.

To use as the parameter in another tag, use the parse parameter in the outer tag:

{exp:channel:entries parse="inward" dynamic="no" category="{exp:piped sql='Select cat_id FROM exp_categories WHERE cat_url_title=^{segment_4}^'}"}

Note the use of ^ in place of a double quote.  Any ^ is string replaced by a ".

Apple
:   Pomaceous fruit of plants of the genus Malus in
    the family Rosaceae.
:   An american computer company.

Orange
:   The fruit of an evergreen tree of the genus Citrus.

