
PipeQ
==========
A fast and multipurpose queuing system for managing the flow of people in environments where maintaining order of entry is required.


About
----------
PipeQ is designed to streamline queue management, providing a robust and flexible system for controlling the order and flow of entry. With PipeQ, administrators can ensure a smooth and fair entry process, reducing wait times and enhancing user experience. It is ideal for spaces where high throughput, clear queuing, and minimal crowding are priorities.


## Use cases
PipeQ can be deployed in a variety of environments, such as:

- Healthcare Facilities: Maintain orderly patient check-in and waiting areas
- Public Services: Ensure efficient queuing for government offices or banks
- Bistro: Manage customer queues at food receiving, preventing crowding and ensuring smooth service.

## Installation
### Docker
#### Prerequisites
Ensure Docker and Docker Compose are installed on your system.

#### Steps
You can run below command to create docker container:
```
docker-compose up -d
```

### Bare metal
PipeQ can also be installed directly on a Linux system. The instructions below assume a Debian-based system but can be adapted for other Linux distributions.  
You can loookup `docker-compose` file for instructions regarding installation on bare metal.

#### Prerequisites
- Make sure PHP, Composer, Node.js, and Apache are installed:
```
sudo apt -y install php composer npm apache2 
```
 - Other system dependencies:
 ```
 sudo apt -y install php-curl
 ```
 
#### Steps
1. Create your .env file from .env.example
```
cp .env.example .env
```

2. Install composer dependencies
```
composer install
```

3. Build the database
```
php artisan migrate:fresh --seed
```

4. Install npm dependencies
```
npm install
```

5. Build your npm packages
```
npm run build
```

5. Move your program files your apache server files
```
cp ./* /var/www/html/.
```

6. Run reverb server
```
php artisan reverb:start
```