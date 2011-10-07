# ALT MultiField #

ALT MultiField is designed for storing smallish bits of data that are associated with each other. 
Think of an address, consisting of separate fields for Street Address, City, State, Zip, and Country; or
a set of stats for a roleplaying game, which might consist of 
Strength, Wisdom, Dexterity, Intelligence, Charisma, and Constitution.

What these have in common is:

1. They are RELATED to each other, so you may want to output individual pieces but you might also 
want to manipulate them as a unit; and
2. You only need one SET of the fields per entry, so a Matrix is overkill (especially if there are more than 
seven or eight, at which point a Matrix row starts to stre-e-e-e-e-tch your Publish form out unpleasantly).

