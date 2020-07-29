=== Tarot Card Oracle ===

Contributors: chillichalli, freemius
Donate link: https://paypal.me/cartouchecards?locale.x=en_US
Tags: tarot, cartouche
Requires at least: 4.6
Tested up to: 5.4
Stable tag: trunk
Requires PHP: 5.6.20
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin lets you create tarot and oracle readings using your own cards, spreads and interpretations.

== Description ==
Used to create your own oracle and tarot readings. Use this plugin to build any spread, using any deck of cards and any 
interpretation of those cards. If cards have different meanings in different positions, it’s possible to define each card 
for each position.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress 
plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Card Oracle->Dashboard General tab to configure the plugin

== Frequently Asked Questions ==
= What is the best way to build a reading =
1. Start by creating a Reading.
2. Create all the card Positions for the Reading.
3. Create all the Cards.
4. Create a Description for each Card in all the different Positions.

== Screenshots ==
1. Card Oracle Dashboard - Dashboard showing statistics for Card Oracle. Giving you the totals of all Readings, Positions, 
Cards, and Descriptions. As well, as breaking down the individual Readings.
2. Add New Reading page - Enter your readings on this page.
3. Add New Positions page - Enter your Positions on this page.
4. Add New Cards page - Enter your Cards on this page.
5. Add New Descriptions page - Enter your Descriptions on this page.
6. Validation wizard (Premium) - Check you Readings, Positions, Cards, and Descriptions for errors and quickly correct them using the links.
7. Email Settings (Premium) - Options for allowing users to subscribe to your list. Daily Card settings for sending cards to your subscribers.
8. Email Providers (Premium) - Settings to configure you subscriber email lists. Supports MailChimp current, more coming soon. Yours not on the list email us and request it.

== Changelog ==
= 0.5 =
* Initial beta release.

= 0.6 =
* Improvement to the Readings, Cards, and Positions checks.
* Code cleanup

= 0.7 =
Bug fixes
* Removed unused code.
* Fixed slider checkbox display
* Fixed sortable field Card Order

Upgrades
* Added Validations for Premium version

= 0.8.1 =
Bug fixes
* Updated Option Name to avoid conflicts with other plugins
* Fixed broken links for the back button.
* Fixed broken links for the Validations.

= 0.8.1 =
* Remove missing file from require_once

Upgrades
* Added MailChimp integration (Premium)

= 0.9.0 =
Bug fixes
* Removed duplicate validation test 

Upgrades
* Added Daily Card email to MailChimp (Premium)

= 0.10.0 =
Bug fixes
* Images in email not displayed properly

= 0.10.1 =
Bug fixes
* Safari css display for front of card fixes

= 0.11.0 =
* Minor bug fixes

== Upgrade Notice ==
= 0.5 =
* Initial beta release.

= 0.6 =
* Minor bug fixes.
* Code cleanup.

= 0.7 =
Bug fixes
* Removed unused code.

Upgrades
* Added Validations for Premium version

= 0.7.1 =
* Added Screenshots

= 0.8.1 =
* Updated Admin Styling
* Added MailChimp integration for Premium version

= 0.9 =
* Added option to email Daily Cards to subscribers.

= 0.10.0 = 
* Card now flip over and displays when the user selects them.

= 0.11.0 = 
* Add demo data for a 3 position past, present, future reading.

== Translations ==

* English - default, always included

== How to ==

The Card Oracle is made up of five different areas:

= Menus =

**Dashboard**
Where the user gets an overview of what&#39;s been configured in the Card Oracle and where they can pick up the Shortcodes 
for their Readings to display on the front end of the site.

**Readings**
Where the user defines the Spreads that are going to be used

**Positions**
Where the user defines the Positions to be used with each specific Reading/Spread. Positions are assigned to a Spread here.

**Cards**
Where the user defines the Cards that will be used with the Readings/Spreads

**Descriptions**
Where the user configures the definitions used for each Card in each specific Position defined in &#39;Positions&#39; above.

**Validation (Premium)**
Shows protential problems with Readings, Positions, Cards, and Descriptions.

= Dashboard =
This provides an overview of the Readings, Positions, Cards and Descriptions that the user has define within their version 
of the 
Card Oracle.

It is a useful quick sanity-checker – if you know how many Cards you have, and you know how many Positions you have for each Card 
for each Reading, you can quickly check that you have a Description defined for every Card in every Position in a specific Reading.

The user must take care to link Descriptions to the correct Cards and Positions. And in turn to link the Positions to the correct 
Reading, but this is a great overall checker – if the numbers do not add up, then you need to check your configuration.

Additional tabs on Dashboard:

General – General Settings
Multiple Positions for a Description – this is the global setting where you can allow the Card Oracle to use the same Position for 
multiple Descriptions within a single Reading and across multiple Readings. The multiple Positions for a Description on the Readings 
tab will not work if this is not enabled.

Email Options
If you want your users to be able to email themselves a copy of their Reading, then toggle the &#39;Allow users to send Reading 
to an email address&#39; option here. Currently this does not interface with any list-building platforms, however it will in future 
versions of the plugin.

From email address – this is where you define the email address from which the Readings will be sent.

From email name – this can be different from the email address above, if you have a site name or want to use your own name.

Text to display
This is where you can configure what text you want to display – it accepts HTML. If this is left blank, the default text reads: 
&quot;Email this Reading to:&quot;

Validation Tab
There are no settings on the Validation tab yet. This is a feature that will be rolled out in future versions of the plugin.

= Readings =
This tab is used to define the different Readings (or Spreads) the user wants to offer on the front end of the site.

Click Add New Card Reading.

**Title**
Add the name of the new Reading.

**Settings**
Settings offer the following options:

Multiple Positions for a Description – this allows you to use the same Description for the same Card in multiple Positions for 
this Reading. e.g. the Card Descriptions for a &#39;Past, Present, Future&#39; Reading. The Description for each Card does not 
necessarily need to change, dependant on the Position, so you can use this toggle to reuse the same Description for multiple 
Positions in one Reading.

If this is left disabled, you will need to create a Description for each Card in each Position that you define for this Reading 
on the Positions tab.

Display Question Input Box - when enabled, this displays a question input box. The submit button will only show (it displays 
after the correct number of Cards have been picked) if text has been entered in this box. If you do not want your user to enter 
a question for this Reading, leave this toggle disabled.

Text for input box - allows you to prompt the user to enter text in the input box - e.g. &#39;What is your question today?&#39;, 
&#39;Where do you need clarity&#39; etc.

**IMPORTANT** - do NOT use apostrophes in your text here, if you plan on allowing users to email the Readings to themselves.

Footer to be displayed on daily and random Card Readings - This is a free text area - you can use it to upsell other Readings or 
to direct your users to another link or Reading.

**Back of Card Image**
On the right panel of the screen, underneath the WordPress standard post content, add an image for the back of the Card. This is 
what will display before your user picks their Cards. There is a default image programmed into the Card Oracle which will display 
if you do not add a Card Image, but you can personalise your Card back here.

To save the Reading, click on the Publish button as you would with a normal WordPress post.

**Top Tip:** To save time, if you are going to offer multiple Readings, create all of them before you start configuring the rest 
of the Card Oracle - you&#39;ll save yourself a lot of clicks.

When you have created your Reading and saved it, return to the main Dashboard tab.

**Shortcode**
You will see a record with your Reading name, and then a Shortcode option.

If you click on the Reading Shortcodes link at the bottom of the record (below the Positions, Cards and Descriptions), you will 
be presented with three Shortcodes:

Reading Shortcode
This displays the backs of the number of Cards that you have defined for this Reading. When the user clicks on the correct number 
of Cards (and may or may not need to enter a question, depending on how you have configured the specific Reading) the submit 
button will display, the user will click the submit button in order to display the results of the Reading.

This is the Shortcode you will use if you have more than one Position in the Reading.

Daily Card Shortcode
Each day, this displays one new Card on the page/post you have used the Shortcode. The Card will be one of the Cards you have 
defined on the Cards tab. The plugin starts with the first Card you created and then it displays them all in the order that you 
created them (time and date order). If you want the Cards to display in a &#39;random&#39; order, you will need to create them 
in a &#39;random&#39; order in the first place. Once the last Card you have defined has been displayed, the Oracle will start 
displaying them all again from the start. Only one Card is displayed for this Reading.

Random Shortcode
This displays one random Card each time you refresh a page. Only one Card is displayed for this Reading.

To the right of each Shortcode, you&#39;ll see a copy button. Click this to copy the Shortcode. Paste the Shortcode into the 
relevant module on your post or page to display it on the front end of the site.

= Positions =
This tab is where you define the different Positions that you will use in your Readings/Spreads. This is where you link 
Positions to Readings/Spreads.

Click on Add New Position.

**Top Tip:** Once you have all of the Readings defined, define all possible Positions you might use and assign them to the 
specific Readings here.

**Title**
Name of Position - this will display on the Reading. For this reason, use a meaningful name. Doing this will also make it 
easier to set up the Descriptions for each Position later in the configuration.

You will see all of the Card Readings you have set up displayed under Settings. This allows you to assign the Position you 
create to multiple Readings at the same time. Tick the boxes for the relevant Readings.

**Card Order**
Currently you need to identify a Position in the same order (number) over all Readings if you are using it in multiple Readings. 
e.g. Past, Present, Future Spread and Success Spread. E.g:

Reading 1 - Past Present Future

Past = Card Order 1

Present = Card Order 2\* (as in Reading below)

Future = Card Order 3

Reading 2 - Success Spread

Major concern or obstacle = Card Order 1

Present situation = Card Order 2\* (as in Reading above)

Hidden Factors = Card Order 3

New Ideas or People that will help = Card Order 4

What you need to do so succeed = Card Order 5

You CANNOT use a different Card Order for the same Position over multiple Readings.

Click Publish to save the Position.

On the Positions index page, you will see which Readings are associated with each Position.

= Cards =
Create the Cards you will use in your Readings here. This is where you link Cards to Readings/Spreads.

Click on Add New Card

**Title**
Add the title of the Card. This will display as the name of the Card on the Reading.

**Text Box**
Below the title, there is a text box. This is used to show definitions for the one Card Daily and Random Readings. If you 
do not enter any text here, nothing other than the Card will show. If you want text to appear for these Readings, you must 
enter it here on the original Card record.

**Settings**
Below the text box, the Settings tab displays all of the Card Readings that you defined in the Readings section of the 
Card Oracle. Click the Readings in which you want to use this Card.

**Front of Card Image**
To the right of the text box, under the normal WordPress post options, you will see an option to Add Card Image. Click this, 
select the image you want to associate with this Card.

Click on Publish to save the Card.

If you want to check which Card is assigned to which Reading, if you click on the overall Cards tab in the Card Oracle menu, 
you will see which Cards are associated with each Reading.
You must associate Cards with a Reading in order for their backs to display (and be selected) on the Reading on the front end 
of the site. If you do not have the right number of Cards displaying for a particular Reading once you have configured it, 
check here that all the Cards required are associated with the Reading.

**Top Tip:** On the Card index page, you will see the Number of Descriptions associated with each Card. This is a fast way to 
check that all of the Cards have the right number of Descriptions.

= Descriptions =
This is the tab where you create individual Descriptions for a specific Card in a specific Position in a specific Reading. 
The link between Position and Reading has already been made (Positions, above). The link between Card and Reading has already 
been made (Cards, above). This is where we link all three elements together.

Click on Add New Card Description.

**Title**
Add a title for your Description. Make your Description title obvious in order to be able to identify exactly what it is. 
This title will display on the Reading.

**Text Box**
Add the content you want to display when you display this specific Card in this specific Position of this specific Reading.

**Settings**
Card
Select the Card you want to associate with this Description from the drop down box. You must associate a Card with a 
Description if you want the Description to display on the front end of the Reading.

Description Position
This displays all of the Positions available to you to associate with this Description. You can associate a single 
Description with multiple Positions.

Click Publish to save the Description.

**Top Tip:** On this main Descriptions index page, you will see a list of Descriptions and the Positions with which you 
have associated them.