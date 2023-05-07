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
- `git clone git@github.com:021800rr/Wallet.git`
- `cd Wallet/`
- `composer install`

- `npm install node-sass sass-loader --save-dev`
- `npm install bootstrap @popperjs/core bs-custom-file-input --save-dev`
- `symfony run npm run dev`

[//]: # (- `yarn install`)
[//]: # (- `yarn add sass-loader@^13.0.0 sass --dev`)
[//]: # (- `yarn add jquery --dev`)
[//]: # (- `yarn add bootstrap --dev`)
[//]: # (- `yarn add controllers --dev`)
[//]: # (- `yarn add @popperjs/core --dev`)
[//]: # (- `symfony run yarn encore dev`)

- `vi .env.local`

- `create database account;`
- `create database account_dev`
- `create database account_dev_test`
- `create user rr with encrypted password 'rr';`
- `grant all privileges on database account to rr;`
- `grant all privileges on database account_dev to rr;`
- `grant all privileges on database account_dev_test to rr;`
- `alter user rr createdb;`

- `symfony console doctrine:migrations:migrate`
- `symfony console doctrine:fixtures:load`

[//]: # (- `symfony server:start -d`)

```
php bin/console --env=test doctrine:schema:create
php bin/console --env=test doctrine:fixtures:load
php bin/phpunit
```

```
php bin/console lexik:jwt:generate-keypair
setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
```