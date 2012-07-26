<?php

$lang = array(

'alt_multifield_options' => 'MultiField Subfields',
'alt_option_setting_examples' => <<<EOS
<p><tt>short_name : Field Label : type : dropdown options | (pipe-delimited)</tt></p>
<ol style="margin-left: 2em;">
<li><tt>short_name</tt> <strong style="color: #ff0000;">(REQUIRED)</strong> 
    follows the same conventions as a URL title</li>
<li><tt>Field Label</tt> <strong style="color: #ff0000;">(REQUIRED)</strong> 
    is a plain text string but probably shouldn't contain quotes</li>
<li><tt>type</tt> <em style="color: #222222; font-style: italic;">(OPTIONAL)</em> should be one of the following values:<br />
    <tt>text</tt>, <tt>tel</tt>, <tt>email</tt>, <tt>url</tt>, <tt>number</tt>, <tt>date</tt>, <tt>textarea</tt>, or <tt>dropdown</tt>.<br />
	(Several of these are HTML5 field types. No JS validation exists on these but modern browsers will check them for you.)<br />
	If you do not enter a value here, it will default to <tt>text</tt>.</li>
<li>If you create a <tt>dropdown</tt>, then add an extra colon and a string of options, delimited by ' | ' (space, pipe, space). There is currently
    no way to have option text and values be different!
</ol>
EOS
,

'alt_multifield_styles' => 'Custom CSS for This Field',
'alt_multifield_styles_examples' => <<<EOS
<p>You may enter custom styles here. All styles will be prefixed with the id of the specific field when
they are output, so anything you enter here will ONLY affect this field's subfields even if there are several
instances of MultiField on your Publish form.</p>
<p>Basic layout of each subfield:</p>
<p>
<code>&lt;li class=&quot;alt-multifield alt-multifield-test-field<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;alt-multifield-box-type-url&quot;&gt;<br />
&lt;label&gt;Test&lt;/label&gt;<br />
&lt;input type=&quot;url&quot; name=&quot;field_id_2[test]&quot; <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;value=&quot;&quot; id=&quot;field_id_2-test&quot;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;class=&quot;alt-multifield-test<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;alt-multifield-input-type-url&quot; /&gt;<br />
&lt;/li&gt; 
</code></p>
<p>Helpful things to note:</p>
<ol style="margin-left: 2em;">
<li>Each wrapper &lt;li&gt; gets a class of <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<tt>alt-multifield-box-type-[TYPE]</tt> ,<br /> 
and each input or textarea gets a class of <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<tt>alt-multifield-input-type-[TYPE]</tt>. 
<br />There is also a class of <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<tt>alt-multifield-[SUBFIELDNAME]-field</tt> <br />
on each wrapper &lt;li&gt; if you need to style one individually.</li> 
<li>If you need to style the OUTSIDE box (that is, the whole &lt;ol&gt;), <br />use <tt> { whatever: whatever; }</tt>
(that's a <em style="color: #222222; font-style: italic;">blank space</em> followed by your bracketed styles.)<br /> 
It's probably best to do this on the first line of the block!</li>
</ol>
EOS
,
''=>''
);