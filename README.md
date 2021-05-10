# Smithereen

Federated, ActivityPub-compatible social network with friends, walls, and groups.

If you have any questions or feedback, there's a [Telegram chat](https://t.me/SmithereenProject) you can join.

## Building/installation

### Running directly on your server

1. Install and configure MySQL
2. Install maven if you don't have it already
3. Build the jar by running `mvn package` and place the one with dependencies at `/opt/smithereen/smithereen.jar`
4. Set up the native library ([libvips](https://github.com/libvips/libvips) and JNI bindings): pick a binary for your OS and CPU from [prebuilt ones](jniPrebuilt) or build your own
5. Install and configure [imgproxy](https://docs.imgproxy.net/#/GETTING_STARTED)
6. Fill in the config file, see a commented example [here](examples/config.properties)
7. Create a new MySQL database and initialize it with the [schema](schema.sql) using a command (`mysql -p smithereen < schema.sql`) or any GUI like phpMyAdmin
8. Run `java -jar /opt/smithereen/smithereen.jar /etc/smithereen/config.properties init_admin` to create the first account
9. Log into that account from your web browser, then configure the rest of the server settings from its UI

### Using Docker

Copy [Docker-specific config example](examples/config_docker.properties) to the project root directory as `config.properties` and edit it to set your domain. Also edit `docker-compose.yml` to add your imgproxy secrets. You can then use `docker-compose` to run Smithereen, MySQL, and imgproxy. You still need to [configure your web server to reverse proxy the port 4567](examples/nginx.conf). Create the first account by running `docker exec -it smithereen_web_1 bash -c ./smithereen-init-admin`.
