# Usa l'immagine base PHP con Apache
FROM php:8.2-apache

# Installa l'estensione mysqli (necessaria per connettersi a MySQL)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Installa anche PDO per compatibilità futura
RUN docker-php-ext-install pdo pdo_mysql

# Installa msmtp per l'invio email
RUN apt-get update && apt-get install -y msmtp msmtp-mta && rm -rf /var/lib/apt/lists/*

# Configura msmtp per usare Mailpit
RUN echo "account default\n\
host mailpit\n\
port 1025\n\
from no-reply@bibliotech.local\n\
auth off\n\
tls off\n\
logfile /var/log/msmtp.log" > /etc/msmtprc

# Configura PHP per usare msmtp come sendmail
RUN echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/mail.ini

# Crea il file di log con i permessi corretti
RUN touch /var/log/msmtp.log && chmod 666 /var/log/msmtp.log

# Copia il codice PHP nel container
COPY ./src /var/www/html

# Imposta la directory di lavoro
WORKDIR /var/www/html