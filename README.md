# Project-I-BCA
For development and collaboration on college's Project-I

To be done:
Hotel Website
This is History themed Resturant Website
It can be used to take order from table on login page 
The customer can read history of the food and the culture early days of the food 

Additionally also maybe:
3d food functionality which can track food protein and the calories or energy of the food
also the label of food ingredients


From sir to add:-
1. Proper Billing and Status of the financial report condition of business overally for profit and loss calculation
2. Fix payment issues if user has many tables or if there are multiple payments from single table.
3. While Registering users to login send confirmation mail and confirm the mail
4. Add user ratings and reviews system for dishes 
5. 

I will reserve this chair there will be unique code when entered one can pay for entire order
but problem is i have to manage the payment status from any of the order
if user selects same group code from different table then also one can pay for all the orders

Payment yesto banaune payment ko duita option xa ki individual bill ko tirne or whole table ko tirne
Khalti bata when payment is clicked khalti ko site ma redirect hunxa with cash already written and just put phone number and mip pin then pay for food personal or whole table if the user wants to pay for multiple users or multiple tables then he/she can just put phone number and mip pin or password to login and pay from redirected link.

 
make changes so that user can also pay individually or in table also set the status for admin to select if paid or not in database it says status like paid or not and if paid already then the button will change it's word to paid. Also, admin panel update the admin code to  

Steps :-
first scan the qr code to get the table number
then login to the website
then click on the menu to see the menu items
then click on the order now to order the food
then click on the confirm order to confirm the order
then click on the checkout to checkout the order
then click on the print reciept to print the reciept


Necessary Updates
1. We must add qr functionality.
2. We must add Payment method.
3. make order quantity increment if added again
4. also add price by quantity
5. Succesful message after every updates not alert everytime
6. Make the table number cookie expire after 3 hour
7. Make the new content be added from above in while showing the orders to admin cook or to users's order new element to top.
8. Auto clear the content of table details of order details like food details, 
<!-- 9. to show orders new order at top. -->
10. Payment method is missing
6. Send a confirmation email or SMS to users after a successful table booking.
        eg. Example email content: "Your table #3 has been successfully booked. Thank you!"
7. Add a feature to allow users to cancel or reschedule their reservations.
8. Add a feature to allow users to view their reservation details and history.
9. Add a feature to allow users to view the menu and make orders.
10. Add a feature to allow users to view the history of the food and the culture early days of the food
11. Add user ratings and reviews system for dishes
12. Implement real-time table availability status
13. Add dietary filters (vegetarian, vegan, gluten-free, etc.)
14. Implement a loyalty/rewards program for regular customers
15. Add estimated waiting time for orders
16. Implement multi-language support for international customers
17. Add allergen information for each dish
18. Create a staff interface for managing orders and tables
19. Implement inventory management system
20. Add analytics dashboard for business insights
21. Implement secure user authentication and authorization
22. Add order status tracking system
23. Create a mobile-responsive design
24. Implement automated order confirmation system
25. Add integration with popular payment gateways
26. Implement input validation for all forms (booking, ordering, user registration)
27. Add error handling and logging system
28. Create backup and recovery system for the database
29. Add session management and timeout functionality
30. Implement CSRF protection for all forms
31. Add rate limiting for API endpoints
32. Create automated testing suite (unit tests, integration tests)
33. Implement caching system for better performance
34. Add data sanitization for all user inputs
35. Create API documentation
36. Add order modification functionality before confirmation
37. Implement table reservation conflict resolution
38. Add automatic table assignment optimization
39. Create kitchen display system (KDS) for order management
40. Add printer integration for receipts and kitchen orders



Unnecessary Changes
1.  Booking Reminder
If the booking is for a future date, send a reminder closer to the time.
2. 3d food functionality which can track food protein and the calories or energy of the food
3. also the label of food ingredients
4. 



These additions focus on:
User Experience (ratings, dietary info, multi-language support)
Business Operations (inventory, analytics, staff interface)
Security (authentication, secure payments)
Technical Quality (mobile responsiveness, order tracking)

<!-- Table Number   might be links -->
http://localhost/Project-I-BCA/book_table.php?table_number=11
Replace 5 with the actual table number for each QR code or link.


<!-- 
Structure of database
Relationships and Foreign Keys in tableserve

orders Table:
Foreign Key: fk_table_id

Column: table_id
References Table: tables
References Column: id
Foreign Key: fk_user_id

Column: user_id
References Table: users
References Column: id
Foreign Key: orders_ibfk_1

Column: user_id
References Table: users
References Column: id (duplicate relationship for user_id).
Foreign Key: orders_ibfk_2

Column: table_id
References Table: tables
References Column: id (duplicate relationship for table_id).
order_items Table:
Foreign Key: fk_menu_item_id

Column: menu_item_id
References Table: menu_items
References Column: id
Foreign Key: fk_order_id

Column: order_id
References Table: orders
References Column: id
Foreign Key: order_items_ibfk_1

Column: order_id
References Table: orders
References Column: id (duplicate relationship for order_id).


Foreign Key: order_items_ibfk_2

Column: menu_item_id
References Table: menu_items
References Column: id (duplicate relationship for menu_item_id).


Summary of Database Relationships
orders is related to:

tables through table_id
users through user_id
order_items is related to:

orders through order_id
menu_items through menu_item_id -->
