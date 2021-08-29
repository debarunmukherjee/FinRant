<p align="center"><a href="https://www.finrant.studio" target="_blank"><img alt="FinRant Logo" src="https://www.finrant.studio/images/Logo.svg?18374b897249bf9c251007f64293d959" width="400"></a></p>


## About FinRant

In our busy everyday lives we don’t always get enough opportunity or time to keep a track of all our day-to-day expenditures. Due to this we sometimes end up spending much more than we intend to and fail to stick to our monthly budgets.

FinRant is an app that helps you to efficiently plan all your expenses ahead of time and also keep tabs on how much you are spending in a specific category by setting monthly budgets. You can create various plans to organise your expenses separately, and also invite other users to join those plans in order to share expenses or even safely transfer money within the app.


- Simple and fast way to record expenses.
- Categorise your expenses under different plans to track them efficiently.
- Collaborate with other members to manage shared expenses at one place.
- Set budgets and get insights from the various visual charts composed of your expense data. 
- Settle your dues or transfer money to other plan members directly from our secured in-app payment gateway.

## Tech Stack

- Backend: PHP with [Laravel](https://laravel.com) framework
- Frontend: [React JS](https://reactjs.org/)
- Other Libraries: [Inertia JS](https://inertiajs.com/) - Helped to couple my single page application frontend to the classic **Route-Controller** architecture of Laravel.
- All other dev dependencies and libraries can be found in the composer and package json files.

## Setting up dev environment

- Install [Docker](https://www.docker.com/) for your corresponding os version
- Install [Composer](https://getcomposer.org/).
- Install [Node](https://nodejs.org/en/)
- Clone the repo in your desired folder.
- Run `composer install` in the cloned repo folder.
- Run `npm install` in the cloned repo folder.
- Configure a bash alias for Laravel Sail composer library, like so `./vendor/bin/sail up` if you don't want to type the entire path to run laravel artisan commands with sail.
- Run `sail up` in the repo directory
- Create a **.env** file and configure the correct values to connect to the mysql docker image. Refer to Laravel docs if you need help, they are super nice!
- Run `sail artisan migrate` to run all the database migrations.
- Try accessing the site by visiting **localhost**.
- Feel free to create an issue if you are stuck anywhere.
- Ensure to fill in the environment values properly as below -
  ```
  FUSION_AUTH_TOKEN=""
  FUSION_FUNDING_ACCOUNT_ID=""
  FUSION_BUNDLE_ID=""
  FUSION_IFI_ID=""
  FUSION_BUNDLE_NAME=""
  ```

## Team Members

- Tech Lead: [Debarun Mukherjee](https://www.linkedin.com/in/debarun-mukherjee-a518a114b/)
- Product Lead and QA: [Anannya Ghosh](https://www.linkedin.com/in/anannya-ghosh-5686a120b/)

## Security Vulnerabilities

If you discover a security vulnerability within the application, please send an e-mail to Debarun Mukherjee via [debarun.mukherjee1997@gmail.com](mailto:debarun.mukherjee1997@gmail.com). All security vulnerabilities will be promptly addressed.
