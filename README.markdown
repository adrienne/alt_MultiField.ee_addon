# ALT MultiField #

## Requirements ##

This addon is **not tested** with versions of PHP prior to 5.3 ! I've tried to make it compatible,
but I cannot make any guarantees about whether it will actually work or not.

## Overview ##

ALT MultiField is designed for storing smallish datasets in a compact and easily-manipulable way. 

Think of the following sorts of data:

* An address, consisting of separate fields for Street Address, Suite/Box/Apartment
  Information,  City, State, Zip, and Country; 
* A set of stats for a roleplaying game, which might consist of such things as Strength, 
  Wisdom, Dexterity, Intelligence, Charisma, and Constitution; or 
* Information for a particular garden plant, such as what kind of Soil it prefers, how much 
  Water it likes, its Shade Tolerance, and how many Days it takes to mature.

What these have in common is:

1. The individual bits of data are RELATED to each other, so you may want to output 
   individual pieces,  but you might also want to manipulate them as a unit; and 
2. You only need one SET of the fields per entry, so a Matrix is overkill (especially if 
   there are more  than seven or eight, at which point a Matrix row starts to stre-e-e-e-e-tch your 
   Publish form out unpleasantly).

You could do something similar, layout-wise, by creating seven or eight individual Publish fields 
in your field group; but they would take up a fair bit more space, even if you shrank them with the 
Publish Layout tools so they were three or four across. ALT MultiField fields can also be 
*individually styled*, either the whole group or individual subfields, within each field instance. 
Separate MultiFields will NOT have their style settings overlap or collide, even if there are 
multiple MultiFields on a single Publish form.

Further, individual Publish fields have no notion of *association*, which is a big strength of this 
Fieldtype. You can always grab an individual item or its label, or several of them, using the 
standard tag pair syntax; but in addition to that, you can manipulate the subfields as a *unit* 
using the single-tag syntax. 

## Installation ##

1. Download the GitHub repository and unzip. Add the `alt_multifield` directory to your 
   `/system/expressionengine/third_party/` directory.
2. Go to Add-Ons -> Fieldtypes and install the ALT MultiField fieldtype.

## Setup ##

The way ALT MultiField works, you create your *Field*, and then you define a number of *Subfields* 
that will hold your actual data. Field creation works as normal; go into one of your Field Groups 
and create a new field, setting it to type ALT MultiField.

At that point, you will see two textareas under Field Options; one is for defining Subfields, and 
one is for styling. The syntax for defining Subfields is very similar to 
[Pixel & Tonic's][1] syntax for setting up Dropdowns, Radio Groups, and 
Checkbox Groups, so it may look familiar to you. A subfield is defined as follows: 

    subfield_name : Subfield Label : input type
    
* The `subfield_name` must follow the same rules as URL Titles and Field Names: no spaces, 
  no special characters except `-` and `_`. 
* The `Subfield Label` may contain spaces and special characters but may not contain 
  newlines. 
* The `input type` is *optional*: if you leave it blank, it will default to `text`. Other 
  valid values  are 'textarea' and several of the HTML5 input types: `tel`, `email`, `url`, 
  `number`, and `date`. 

  Note that only *modern* browsers will validate the types; no Javascript backup or shim is in place 
  to catch older browsers. Also note that the `date` type will have the standard EE Datepicker 
  attached to it automatically.  

### Subfield CSS

You can style each ALT MultiField by pasting CSS into the second field option. To ensure your styles target the correct subfields, all CSS rules will be prefixed with the ID of the specific field (or cell in the case of a Matrix cell).  This ID is also applied to the field's output:

	<ol class="alt-multifield-wrapper"
		id="alt-multifield-field_id_2">
		
		// each subfield here as an li, see below
		
	</ol>

The basic layout of a subfield defined as `"breakfast : What is for breakfast? : url"`:

    <li class="alt-multifield
               alt-multifield-breakfast-field
               alt-multifield-box-type-url">

        <label>What is for breakfast?</label>

        <input type="url"
               name="field_id_2[breakfast]"
               value=""
               id="field_id_2-breakfast"
               class="alt-multifield-breakfast alt-multifield-input-type-url" />
    </li>

In the case of a Matrix fieldtype, the markup would look like so (note the use of `-cell` instead of `-field` for the LI class value):

	<li class="alt-multifield
			   alt-multifield-breakfast-cell
		   	   alt-multifield-box-type-url">

		<label>What is for breakfast?</label>

		<input type="text"
			   name="field_id_2[row_id_123][col_id_1][breakfast]"
		   	   value=""
	   	       id="field_id_36[row_id_123][col_id_1]-breakfast"
   	       	   class="alt-multifield-breakfast
  	       	   alt-multifield-input-type-url">
	</li>

Therefore, the following style will target your field's elements:

	.alt-multifield-breakfast-field label {
		font-style: italic;
	}
	
	.alt-multifield-breakfast-field input {
		border: 0;
		border-bottom: 1px solid #900;
	}

Which on the Publish page will be automatically turned into:

	#alt-multifield-field_id_2 .alt-multifield-breakfast-field label {
		font-style: italic;
	}
	
	#alt-multifield-field_id_2 .alt-multifield-breakfast-field input {
		border: 0;
		border-bottom: 1px solid #900;
	}

**NOTE: Define one selector rule at a time to ensure the ID is correctly prefixed to each style declaration.**

Other helpful things to note:

1. Each wrapper <li> gets a class of `alt-multifield-box-type-[TYPE]`, and each input or textarea gets a class of `alt-multifield-input-type-[TYPE]`.
1. There is also a class of `alt-multifield-[SUBFIELDNAME]-field` on each wrapper <li> if you need to style one individually. In the case of a matrix cell, the class is `alt-multifield-[SUBFIELDNAME]-cell`.
1. If you need to style the OUTSIDE box (that is, the whole <ol>), use ` { whatever: whatever; }` (that's a blank space followed by your bracketed styles.) It's probably best to do this on the first line of the block!

In the case of a Matrix fieldtype, the markup would look like this:

	<li class="alt-multifield
			   alt-multifield-breakfast-cell
		   	   alt-multifield-box-type-url">

		<label>What is for breakfast?</label>

		<input type="text"
			   name="field_id_2[row_id_123][col_id_1][breakfast]"
		   	   value=""
	   	       id="field_id_36[row_id_123][col_id_1]-breakfast"
   	       	   class="alt-multifield-breakfast
  	       	   alt-multifield-input-type-url">
	</li>


## Output Info ##

There are two ways to output the contents of a MultiField: the tag pair or the single tag. The tag 
pair gives you flexibility; the single tag is a quick-and-dirty way to handle some common use cases.

So, first, let's say we have a MultiField named `my_multifield`, with Subfields defined as follows:

    subfield_one : A Subfield : text
    subfield_two : Another Subfield : text
    subfield_date : An Arbitrary Date : date
    
Let's take a look at how to output that in various useful ways.

### Tag Pair ###
    
You can output subfields, their labels, and their types using the tag pair syntax, as follows:

    {my_multifield}
        {subfield_one:label} : {subfield_one} (type: {subfield_one:type})
    {/my_multifield}
    
The tag pair takes no parameters.

### Single Tag ###

The single tag outputs your ENTIRE list of subfield labels and values (optionally excluding empty 
values) in a couple of different useful formats.

The syntax for the single tag looks like this:

    {my_multifield style="table" show_empty="yes" include_wrapper="yes" 
                   subfield_classes="odd|even"}
    
* The `style` parameter currently has two values:  `table`, which outputs table rows, and 
  `dl`, which outputs a definition list. If you fail to include a `style` parameter it will default 
  to `table`.
* The `show_empty` parameter determines whether the tag outputs subfields whose values are 
  empty; it defaults to `no`.
* The `include_wrapper` parameter determines whether wrapper `<table></table>` or 
  `<dl></dl>` tags are output at the beginning and end of the loop; it defaults to `yes`.
* The `subfield_classes` parameter takes a **pipe-delimited** list of classes that will be 
  applied in order to the subfields as it loops through. It works much like the EE `{switch}` tag. 
  In the example above, the first subfield will get class `odd` as it loops, the second will get 
  class `even`, the third will get `odd` again, and so forth. If you do not add any classes to this 
  loop, all of your subfields will get class `multifield`. Note that the value of the `style` tag 
  may cause additional classes to be applied to portions of the output as well!

#### Example Tag Outputs ####

The following single tag, applied to our example field above:

    {my_multifield style="dl" show_empty="yes" include_wrapper="yes"}
    
Will return the following output:

    <dl>
        <dt class="multifield label">A Subfield</dt>
            <dd class="multifield value">(some value)</dd>
        <dt class="multifield label">Another Subfield</dt>
            <dd class="multifield value">(some value)</dd>
        <dt class="multifield label">An Arbitrary Date</dt>
            <dd class="multifield value">YYYY-MM-DD hh:ii:ss aa</dd>
    </dl>

The following single tag, applied to our example field above:

    {my_multifield style="table" show_empty="yes" include_wrapper="no" subfield_classes="odd|even"}
    
Will return the following output (note that `include_wrapper` is set to `no`, meaning the wrapper 
`<table></table>` tag is *not* returned!):

        <tr class="odd">
            <td class="label">A Subfield</td>
            <td class="value">(some value)</td>
        </tr>
        <tr class="even">
            <td class="label">Another Subfield</td>
            <td class="value">(some value)</td>
        </tr>
        <tr class="even">
            <td class="label">An Arbitrary Date</td>
            <td class="value">YYYY-MM-DD hh:ii:ss aa</td>
        </tr>

**NOTE:** Dates are output as standard MySQL dates and there is currently no built-in formatting 
capability. There are other plugins that will allow you to format arbitrary dates using EE format 
codes; use them with the tag pair syntax! [Nice Date][2] by Low is a good choice for this. 

[1]: http://pixelandtonic.com/
[2]: http://devot-ee.com/add-ons/nice-dat_

## Overview/To-Do List ##

* Enable select boxes (somehow)
* Better settings screen (I stole this one from P&T's free addons; I'd rather have it be cleaner and prettier)