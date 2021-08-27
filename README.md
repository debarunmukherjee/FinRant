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

## License

                   GNU LESSER GENERAL PUBLIC LICENSE
                       Version 3, 29 June 2007

Copyright (C) 2007 Free Software Foundation, Inc. <https://fsf.org/>
Everyone is permitted to copy and distribute verbatim copies
of this license document, but changing it is not allowed.


This version of the GNU Lesser General Public License incorporates
the terms and conditions of version 3 of the GNU General Public
License, supplemented by the additional permissions listed below.

0. Additional Definitions.

As used herein, "this License" refers to version 3 of the GNU Lesser
General Public License, and the "GNU GPL" refers to version 3 of the GNU
General Public License.

"The Library" refers to a covered work governed by this License,
other than an Application or a Combined Work as defined below.

An "Application" is any work that makes use of an interface provided
by the Library, but which is not otherwise based on the Library.
Defining a subclass of a class defined by the Library is deemed a mode
of using an interface provided by the Library.

A "Combined Work" is a work produced by combining or linking an
Application with the Library.  The particular version of the Library
with which the Combined Work was made is also called the "Linked
Version".

The "Minimal Corresponding Source" for a Combined Work means the
Corresponding Source for the Combined Work, excluding any source code
for portions of the Combined Work that, considered in isolation, are
based on the Application, and not on the Linked Version.

The "Corresponding Application Code" for a Combined Work means the
object code and/or source code for the Application, including any data
and utility programs needed for reproducing the Combined Work from the
Application, but excluding the System Libraries of the Combined Work.

1. Exception to Section 3 of the GNU GPL.

You may convey a covered work under sections 3 and 4 of this License
without being bound by section 3 of the GNU GPL.

2. Conveying Modified Versions.

If you modify a copy of the Library, and, in your modifications, a
facility refers to a function or data to be supplied by an Application
that uses the facility (other than as an argument passed when the
facility is invoked), then you may convey a copy of the modified
version:

a) under this License, provided that you make a good faith effort to
ensure that, in the event an Application does not supply the
function or data, the facility still operates, and performs
whatever part of its purpose remains meaningful, or

b) under the GNU GPL, with none of the additional permissions of
this License applicable to that copy.

3. Object Code Incorporating Material from Library Header Files.

The object code form of an Application may incorporate material from
a header file that is part of the Library.  You may convey such object
code under terms of your choice, provided that, if the incorporated
material is not limited to numerical parameters, data structure
layouts and accessors, or small macros, inline functions and templates
(ten or fewer lines in length), you do both of the following:

a) Give prominent notice with each copy of the object code that the
Library is used in it and that the Library and its use are
covered by this License.

b) Accompany the object code with a copy of the GNU GPL and this license
document.

4. Combined Works.

You may convey a Combined Work under terms of your choice that,
taken together, effectively do not restrict modification of the
portions of the Library contained in the Combined Work and reverse
engineering for debugging such modifications, if you also do each of
the following:

a) Give prominent notice with each copy of the Combined Work that
the Library is used in it and that the Library and its use are
covered by this License.

b) Accompany the Combined Work with a copy of the GNU GPL and this license
document.

c) For a Combined Work that displays copyright notices during
execution, include the copyright notice for the Library among
these notices, as well as a reference directing the user to the
copies of the GNU GPL and this license document.

d) Do one of the following:

       0) Convey the Minimal Corresponding Source under the terms of this
       License, and the Corresponding Application Code in a form
       suitable for, and under terms that permit, the user to
       recombine or relink the Application with a modified version of
       the Linked Version to produce a modified Combined Work, in the
       manner specified by section 6 of the GNU GPL for conveying
       Corresponding Source.

       1) Use a suitable shared library mechanism for linking with the
       Library.  A suitable mechanism is one that (a) uses at run time
       a copy of the Library already present on the user's computer
       system, and (b) will operate properly with a modified version
       of the Library that is interface-compatible with the Linked
       Version.

e) Provide Installation Information, but only if you would otherwise
be required to provide such information under section 6 of the
GNU GPL, and only to the extent that such information is
necessary to install and execute a modified version of the
Combined Work produced by recombining or relinking the
Application with a modified version of the Linked Version. (If
you use option 4d0, the Installation Information must accompany
the Minimal Corresponding Source and Corresponding Application
Code. If you use option 4d1, you must provide the Installation
Information in the manner specified by section 6 of the GNU GPL
for conveying Corresponding Source.)

5. Combined Libraries.

You may place library facilities that are a work based on the
Library side by side in a single library together with other library
facilities that are not Applications and are not covered by this
License, and convey such a combined library under terms of your
choice, if you do both of the following:

a) Accompany the combined library with a copy of the same work based
on the Library, uncombined with any other library facilities,
conveyed under the terms of this License.

b) Give prominent notice with the combined library that part of it
is a work based on the Library, and explaining where to find the
accompanying uncombined form of the same work.

6. Revised Versions of the GNU Lesser General Public License.

The Free Software Foundation may publish revised and/or new versions
of the GNU Lesser General Public License from time to time. Such new
versions will be similar in spirit to the present version, but may
differ in detail to address new problems or concerns.

Each version is given a distinguishing version number. If the
Library as you received it specifies that a certain numbered version
of the GNU Lesser General Public License "or any later version"
applies to it, you have the option of following the terms and
conditions either of that published version or of any later version
published by the Free Software Foundation. If the Library as you
received it does not specify a version number of the GNU Lesser
General Public License, you may choose any version of the GNU Lesser
General Public License ever published by the Free Software Foundation.

If the Library as you received it specifies that a proxy can decide
whether future versions of the GNU Lesser General Public License shall
apply, that proxy's public statement of acceptance of any version is
permanent authorization for you to choose that version for the
Library.
