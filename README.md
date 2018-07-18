##### Status Branch develop
[![Build Status](https://travis-ci.org/DerKnerd/Jinya-Gallery-CMS.svg?branch=develop)](https://travis-ci.org/DerKnerd/Jinya-Gallery-CMS)
[![codecov](https://codecov.io/gh/DerKnerd/Jinya-Gallery-CMS/branch/develop/graph/badge.svg)](https://codecov.io/gh/DerKnerd/Jinya-Gallery-CMS)
[![Dependency Status](https://www.versioneye.com/user/projects/59f1b96415f0d71dedfa1aed/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/59f1b96415f0d71dedfa1aed)
[![StyleCI](https://styleci.io/repos/107044619/shield?branch=develop)](https://styleci.io/repos/107044619)
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2FJinya-CMS%2FJinya-Gallery-CMS.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2FJinya-CMS%2FJinya-Gallery-CMS?ref=badge_shield)

##### Status Branch master
[![Build Status](https://travis-ci.org/DerKnerd/Jinya-Gallery-CMS.svg?branch=master)](https://travis-ci.org/DerKnerd/Jinya-Gallery-CMS)
[![codecov](https://codecov.io/gh/DerKnerd/Jinya-Gallery-CMS/branch/master/graph/badge.svg)](https://codecov.io/gh/DerKnerd/Jinya-Gallery-CMS)
[![Dependency Status](https://www.versioneye.com/user/projects/59f1b9672de28c14954f8cf8/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/59f1b9672de28c14954f8cf8)
[![StyleCI](https://styleci.io/repos/107044619/shield?branch=master)](https://styleci.io/repos/107044619)

# Jinya Gallery CMS
Jinya Gallery CMS is a simple Content Management System made for artists. The idea is to give easy ways to add your artworks and position them in galleries. The whole system is developed by me and my artist friend [Jenny Jinya](http://jenny-jinya.com). Her website is an example of the CMS in action.

# Technologies used
The CMS is based on PHP and Symfony. The frontend code is written in TypeScript and SCSS. Most of the styles are inspired by [Codrops](http://tympanus.net/codrops).

# Deploy
If you want to deploy the CMS just extract the release ZIP and navigate to the desired URL. After that the setup wizard starts and guides you through the installation.

# Update
To update the CMS just copy everything in the folder src, bin and themes to your webserver. After that delete the folder `var/cache` and then open the website.

Please see the changelog for the changes, in the case of database changes update the database via Symfony CLI with the command `php bin/console doctrine:schema:update --force`.

After that the site should be updated. 

# Contribute
If you want to contribute just fork the repository and create a pull request.

# License
[MIT License](LICENSE)

[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2FJinya-CMS%2FJinya-Gallery-CMS.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2FJinya-CMS%2FJinya-Gallery-CMS?ref=badge_large)