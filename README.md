# Connect-i test project.

It is a test project to company Connect-i.

## Test task

Test tasks id directory /tasks.

## Project review

The project was developed on DDEV.
The localhost environment is shared by [Egrok][Egrok].
This link is temporary. It can be available only when my PC is working.
If the link doesn't work, don't hesitate to get in touch with me and I'll enable my PC.

## Credential

**_Super admin_**: admin / admin

## Instruction to install at localhost

1. Navigate to your "projects" directory.
2. Clone repository from [GitHub][Repo].

`git clone git@github.com:bedstvie/connect_i_test.git`

3. Switch to connect_i_test directory.

`cd connect_i_test`

4. Start project in ddev.

`ddev start`

5. Update libraries and packets via composer.

`ddev composer update --prefer-dist`

6. Import database via command or install manually using phpmyadmin. Database preset in repository in backups directory.

`ddev import-db --file=backups/db.sql.gz`

7. Run project

`ddev launch`

[Egrok]: https://7696-46-219-205-215.ngrok-free.app
[Repo]: https://github.com/bedstvie/connect_i_test
