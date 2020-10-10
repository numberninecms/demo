![NumberNine Logo](./assets/images/NumberNine512_slogan.png)

# Disclaimer

NumberNine CMS is still in heavy development. Please don't expect it to work without a hitch.
Any help is welcome: feel free to submit issues or to get involved in the project development.

# Demo application

This is a demo project for testing NumberNine CMS.
For more info please see [this repository](https://github.com/numberninecms/cms).

# Installation

```sh
git clone git@github.com:numberninecms/demo.git
```

Create a `.env.local` file in the root directory then configure your database:
```env
DATABASE_URL=mysql://db_user:db_password@localhost:3306/numbernine?serverVersion=5.7
```

Then launch installation:
```sh
make install
```

If you have errors regarding database (this will be fixed soon with an installation wizard):
```sh
make db
composer dumpautoload
bin/console assets:install public --symlink
make cc
```

# Docker
Not tested for some time. May work, may not work.

# License
[MIT](./LICENSE)

Copyright (c) 2020, William Arin
