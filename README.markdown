# ALT MultiField #

ALT MultiField is designed for storing smallish bits of data that are associated with each other. 
Think of the following sorts of data:

* An address, consisting of separate fields for Street Address, Suite/Box/Apartment Information,
 City, State, Zip, and Country; 
* A set of stats for a roleplaying game, which might consist of such things as 
Strength, Wisdom, Dexterity, Intelligence, Charisma, and Constitution; or
* Information for a particular garden plant, such as what kind of Soil it prefers, how much Water it likes,
 its Shade Tolerance, and how many Days it takes to mature.

What these have in common is:

1. The individual bits of data are RELATED to each other, so you may want to output individual pieces,
 but you might also want to manipulate them as a unit; and
2. You only need one SET of the fields per entry, so a Matrix is overkill (especially if there are more than 
seven or eight, at which point a Matrix row starts to stre-e-e-e-e-tch your Publish form out unpleasantly).


