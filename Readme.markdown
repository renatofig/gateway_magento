# Gateway Magento

## Instruções de Instalação

* Precisa criar a pasta app/code/community dentro da pasta do magento.
* Colocar o diretório desse plugin app/code/community/Locaweb dentro de app/code/community
* Colocar o diretório app/etc/modules para dentro do magento(nessa mesma hierarquia de pastas)
* Colocar o diretório lib para dentro do magento(nessa mesma hierarquia de pastas)
* Colocar o diretório app/locale para dentro do magento(nessa mesma hierarquia de pastas)

## Instalação

Faça um backup antes da pasta do magento(/var/www/magento).

Segundo os testes que fiz, seria assim:

     cp -R app /var/www/magento/app
     cp -R lib /var/www/magento/lib

Somente para esclarecer, caso  sobre a arvore de diretórios, a pasta tem que ficar assim:

 └── magento
     └── app
         └── code
             └── community
                 ├── Locaweb
                 │   └── Abstract
                 │   └── CieloBuyLoja
                 │   └── RedeCardWS
         └── etc
             └── modules
                 │   └── Locaweb_CieloBuyPageLoja.xml
                 │   └── Locaweb_RedecardWS.xml
         └── locale
             └── pt_BR
                 │   └── Locaweb_Gateway.csv
     └── lib
         └── LocawebGateway
             └── ... ... ...

# Precisa verificar as permissões e donos das pastas

* /var/www/magento/app/code/community/
* /var/www/magento/app/etc/modules/
* /var/www/magento/app/locale/pt_BR/
* /var/www/magento/lib/LocawebGateway/

Obs.: Precisa ter permissão de leitura e escrita as pastas, sendo que o owner desses diretórios deve ser o mesmo cara que roda o Apache.
