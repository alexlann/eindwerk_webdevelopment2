# Wishlist
## Author
Alexander Lannoy<br>
For Web Development II (2021-2022) - Frederick Roegiers - Arteveldehogeschool<br>
<br>
## Functionality
1. The admin can scrape categories and products. He can see when a category was last scraped, and scrape the prices again if he pleases. He can also view all the products<br>
2. When the user first registers, he has to make a wishlist. He can edit this later. The user can add products to their wishlist, edit quantity and give a score to the product. He can create a link to send to other people so they can access the wishlist and order products on it. The user can see who ordered a product and can keep count of the total price (ordered and not) of all the products on their wishlist. When he closes the wishlist, a mail is send to notify the user and the admin.<br>
3. The visitor can log in with a given password (received from the user) and view all products on the matching wishlist. They can order products and write a message to the parents. After paying for the product (does not work sadly) the user and the visitor both get a confirmation email.<br>
<br>
## Deployment
<br>
- composer install<br>
- npm install<br>
- php artisan migrate<br>
Fill in .env with mail service (+ generate key)<br>
- php artisan serve<br>
Register new user & change isAdmin in database to 1<br>
Go to menu and click on "scrape"<br>
Scrape all 3 webshops for categories<br>
Scrape the categories you want for data (you can filter between webshops if you want)<br>
You can now register as a user and use the app as you like
