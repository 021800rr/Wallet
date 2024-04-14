# Wallet

Program pozwala kontrolować stan konta oraz nim zarządzać.

Zdefiniowane są dwa konta: **Portfel** i **Backup**.
Backup ma dwa subkonta: **Backup** „właściwy” i **Wakacje**…

Operacja **Przelew** pozwala na przesłanie części środków między kontami.
Przelewając środki z Portfela do Backupu, dzielimy je automatycznie po połowie: 
na Backup „właściwy” i Wakacje.
Przelewając środki z Backupu do Portfela, zdejmujemy tylko środki z subkonta 
Wakacje. Backup „właściwy” pozastaje nienaruszony.

Przez pewien czas używany był moduł do dodawania odsetek...

Czas pokazał, że konieczne było dołożenie kont walutowych.
Teraz Backup przechowuje informacje o odłożonych złotówkach, ale Saldo, Wakacje i Backup
mają stan zerowy. Złotówki zamieniane są na waluty.
"Nie ma żadnej inflacji. Grozi nam deflacja."

Używając funkcji **Opłaty Stałe**, można zaplanować comiesięczne wydatki i przed 
nadejściem nowego miesiąca dodać je do portfela. Funkcja jest cykliczna, 
powtarzalna każdego miesiąca. _(Dlatego w tej części programu, w polu data 
wpisujemy tylko dzień, a miesiąc i rok są uzupełniane automatycznie.)_

Każda transakcja musi mieć zdefiniowanego **Kontrahenta**.
Jeden Kontrahent może obsłużyć wiele transakcji.

Pojawił się także moduł do kontroli odkładanych kwot do backupu.

O ile wprowadzimy przychody do Portfela i rzetelnie wpiszemy wydatki (planując 
opłaty stałe) możemy cieszyć się wzrastającymi zasobami na wakacje...

---  
 
```shell
git clone https://github.com/021800rr/Wallet.git

cd Wallet/
vi .env.dev.local
    e.g.:
        DATABASE_URL="postgresql://rr:rr@postgres-service:5432/account_dev?serverVersion=16&charset=utf8"

        NGPORTS=8000:80
        POSTGRES_DB=account_dev
        POSTGRES_USER=rr
        POSTGRES_PASSWORD=rr
        POSTGRES_PORTS=54320:5432
        DOCKER_COMPOSE_ENV=dev-dev
        
vi .env.test.local
    e.g.:
        DATABASE_URL="postgresql://rr:rr@postgres-service:5432/account_dev?serverVersion=16&charset=utf8"

        NGPORTS=8000:80
        POSTGRES_DB=account_dev
        POSTGRES_USER=rr
        POSTGRES_PASSWORD=rr
        POSTGRES_PORTS=54321:5432

vi .env.prod.local
   e.g.:
        DATABASE_URL="postgresql://user:pass@postgres-service:5432/account?serverVersion=16&charset=utf8"

        NGPORTS=80:80
        POSTGRES_DB=account
        POSTGRES_USER=user
        POSTGRES_PASSWORD=pass
        POSTGRES_PORTS=54322:5432
        DOCKER_COMPOSE_ENV=dev-main

docker compose --env-file .env.prod.local up -d

docker exec -it wallet-php-dev-main bash
    cd /var/www/
    composer install

docker exec -it wallet-postgres-dev-main bash 
    psql -U user -d account
        create database account_dev;
        create database account_dev_test;
        create user rr with encrypted password 'rr';
        ALTER USER rr WITH SUPERUSER;

docker compose --env-file .env.prod.local down

git co develop

docker compose --env-file .env.dev.local up -d
docker exec -it wallet-php-dev-dev bash
    cd /var/www/
    ./reset_dev.sh

    php bin/console lexik:jwt:generate-keypair
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    
    npm install
    npm run dev
```

## test

```shell
git co develop
docker compose --env-file .env.dev.local up -d
./xdebug-disable-enable.sh disable
docker exec -it wallet-php-dev-dev bash
    cd /var/www/

    mkdir --parents tools/php-cs-fixer
    composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
    
    make tests
```

## dev

user/pass: rr/rr

http://localhost:8000/  
http://localhost:8000/api

## prod

```shell
docker exec -it postgres-container bash 
    psql -U postgres_user -d postgres_database < backup_YYYY-MM-DD.sql
```

http://localhost
