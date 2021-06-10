A mock shop API project

#### Usage
This project uses following things
	- Laravel: The main framework
	- Sanctum: For Token Authentication
	- Compser
	- PHPUnit

First, create an empty database and update details in .env file in project core directory. Please run following commands after that to prepare project

- composer update
- php artisan migrate:fresh --seed
- php artisan serve
- php artisan test

### API Endpoints

#### Unauthenticated:
**POST: /register**  
Description: Creates a new user  
Params: email, password, name  

Please note that a user also has levels. Level=1 is admin. An admin user is created by default when you run database seeds. Admin is the only one with access to removed / expired cart items.  

**POST /login**  
Description: Login a user and return token  
Params: email, password  

#### Normal User (requires API token):
**GET: /user**
		Description: Returns authenticated admin data

**GET: /products**
		Description: List all products

**POST: /products**
		Description: Creates a new product
		Params: name, price

**DEL: /products**
		Description: Removes a product
		Params: product_id

**GET: /products/{product}**
		Description: List a single product
		Params: product_id

**POST: /products/{product}**
		Description: Edit a product
		Params: product_id, name, price

**GET: /cart**
		Description: List everything in cart

**POST: /cart**
		Description: Add an item to cart
		Params: product_id


**DEL: /cart/{product}**
		Description: Remove an item from cart
		Params: product_id

**GET: /checkout**
		Description: Mimicks checkout process

#### Admin User
**GET: /admin**
		Description: List quick sales stats

**GET: /admin/orders**
		Description: Lists all orders

**GET: /admin/orders/removed**
		Description: Get orders user added but later removed

