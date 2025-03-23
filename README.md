
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

## Tech Stack

**Backend**
- Laravel 11
- Laravel Reverb (WebSocket)
- SQLite3
- Supervisor

**Frontend**
- Vue.js + Pinia
- Tailwind CSS + Bootstrap
- VueDraggable
- Vite
- Axios + Alpine.js

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
- Make sure PHP, Composer, Node.js, and nginx are installed:
```
sudo apt -y install php composer npm nginx supervisor git php-curl php-mbstring
```

#### Steps
You can copy and paste below code to create your own instance of PipeQ:
```
git clone https://github.com/Kennene/pipeq pipeq
cd pipeq
cp .env.example .env && touch database/database.sqlite
composer install
php artisan key:generate && php artisan migrate --seed
npm install && npm build
```

Then you have to configure nginx and supervisor to run websocket server. Default nginx configuration is already in project files:
```
sudo cp nginx.conf /etc/nginx/sites-available
sudo ln -s /etc/nginx/sites-available /etc/nginx/sites-enabled
```

Note that the WebSocket server must be started with php artisan reverb:start. You can manage it however you like, but using Supervisor is the recommended approach. You can reuse the relevant section from the provided supervisord.conf file:
```
[program:reverb]
command=php artisan reverb:start
directory=/var/www/html
autostart=true
autorestart=true
user=www-data
stdout_logfile=reverb.log
```