# Jurgensville Coding Challenge

## Prerequisites
Developed on PHP 5.5.  Tested on PHP 5.4
To run unit tests, need to have PHPUnit installed

## Execution
### Command Line Execution
from the install directory:

php fulfillment.php pathToDataFile desiredItem1 desiredItem2 ... desiredItemX
## Requirements
[Link to PDF Requirement Document](https://github.com/bhoover10001/Jurgensville/blob/master/SWECodingChallenge.pdf?raw=true)

The data file is a Comma Separated Value (csv) file.  It should not have a header.  Each row should look something like:

1, 10.0, item, item, ... item

The first column is an integer representing the restaurant.
The second column is the price
After the second column, there can be one or more items.  If there is one item, this is an item.  If there is more than one item, this is a meal plan with all the items.



## Assumptions and Execution
This is based off of the workflow that Fred pioneered many years ago.  Fred was our go-to guy at the Jurgensville Chamber 
of Commerce and should not be confused with Fred who used to be a hero in Brazil but is now being booed off the field.

All you had to do was tell Fred what you wanted to eat, and after a few minutes, he would come back and tell you the
cheapest restaurant in Jurgensville that had everything you wanted.  It was astounding.  We're actually not sure what else Fred did, but he always knew the cheapest restaurant.

Unfortunatly, Fred, who was like 150 years old, died recently, and a lot of people in Jurgensville started to go hungry, because they no longer knew which restaurant in town provided the best deal.  It was terrible.

So, we went to his desk, and were astounded.  It turns out that Fred didn't have the photographic memory that everyone thought he did.  He had a bunch of notecards, each notecard had either an item on it or a meal plan that listed all the 
items.  The notecard also held the price and the restaurant name.  Fortunatly, in Jurgensville, we forced our restaurants to all have numbers for names and required the names to be unique, so we could look at the number and know exactly which restaurant Fred was talking about.  Just imagine the confusion if we had to figure out that restaurant 1 was actually Billy's BBQ joint.  The city down the road thinks that we're silly to have this zoning law.  We don't have all those pesky copyright challenges where Billy's BBQ joint says that Bill's BBQ joint is infringing.  Who has time for that?

Well, we went to the head honchos in Jurgensville and said we could computerize this.  They were nice enough to give us Tina's old 286 machine, and we were able to get PHP running on it.  They wouldn't let us take that mySQL class or MongoDB class that we wanted.  They seemed to think that we might take our training and move to that city down the road, so we bore down and got to work.

Anyway, Fred's process:

He would pull out the stack of notecards and scan through them.  If the notecard had food on it that matched the requested meal, he would jot down that restaurant, meal and price onto another notecard, indexed by restaurant name.  He would then check that card to see if he could create a full meal from it and if the price was better than a price he had already found.  If it was, he would pull that card and hold it until he found a better price.

After he had scanned through all the records, he would have a single notecard with the best price for the items.

Well, we just had to do something, and the process worked for Old Fred, so we just decided to automate his workflow.

Now, the assumptions:

Not all of Fred's notecards were complete or valid.  Fred was getting a little.... odd.. at the end... and would sometimes just randomly jot down stuff on one of his notecards.  Well, we didn't have time to review all the cards, so we just computerized them all.  We did build in a business rule to filter out the invalid notecards.

Just like Fred, we always start from the notecards.  None of that silly indexing or catalogs for us.  That doesn't mean that we couldn't load the data somewhere, like a card catalog, index by item or restaurant, but then we would need so many more notecards.  Now, being a little clever, and having, well, spied on the town down the road, we know that we might need to make sure that the system might be a little faster in the future, so we did architect the system to improve the data loader in the future, but there are so many restaurants in Jurgensville, we probably won't ever have time for that.

Occasionally, Fred would also misplace his notecards.  We're pretty sure he would just make up something then.  It didn't happen often, but Restaurant 3 did not have curry_chicken, no matter what Fred said.  We did change this work flow, so that the system would just admit that it couldn't find the notecards.  The system should not be afraid to admit failure - that's how it gets better.

Fred had a very specific way of writing down his notecards, so users of the system are just going to have to get used to it.  All items were lowercase and all spaces were replaced by underscores.  So, if you want a Mushroom Hamburger, you are going to have to ask for mushroom_hamburger.  

We have no idea if Fred wrote down all the item names correctly, but that's part of the fun of our system.  Did he write down mushroom_burger when he meant mushroom_humburger?  Who knows?  I will say that Fred was VERY litteral.  If you asked for a granny_smith_apple, he would ONLY look for granny_smith_apples, not red_delicious_apples, not pears, just granny_smith_apples. 

