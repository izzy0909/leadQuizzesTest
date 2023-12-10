#### Project setup
  * Run Composer create network command from the console
    * `docker network create coding`
  * Run Composer up command
    * `docker-compose up -d`
    
#### API Documentation
  * You can find all API calls here
    * `http://localhost:18030/api/docs`
    

#### LeadQuizzes BACKEND coding challenge

  * Introduction
  
  Your local electronics store has started to expand, but track their entire inventory by hand. They have asked you to build a simple cataloging system as a REST API so that they can integrate with mobile and desktop applications in the future.
  The coding challenge MUST be done in Symfony 3.4 LTS (any other verison is not allowed), but you are free to use any bundle in order to complete the challenge. You should use MySQL as your data layer. Please, use Docker to build your solution

  * Requirements

  The API should be able to: list all products list all categories retrieve a single product creates a product update a
  product delete a product 

  * Authentication

  Only authenticated users can create, update, or delete a product. No authentication is required to retrieve or list.

  * Data

  All entities should have timestamp fields (created_at, and modified_at) Products have the following attributes: name category SKU price * quantity Categories have the following attributes: * name

  * Seed Data

  Please create service (handler) that will import and parse contents of
  (https://lq3-production.s3-us-west-2.amazonaws.com/coding-challenge/data-fixtures.json) into your database. It's up to you how you want to construct relations.

  * Criteria
    * For full transparency, the test will be scored according to the following:
        * REST Structure
        * Logging
        * Use of services, controllers, and models
        * Best practices
        * Reusable code
        * Decoupled code
        * Ability to transform requirements into code