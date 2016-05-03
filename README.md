# mapa.desastre.ec

[![Tareas listas para desarrollo](https://badge.waffle.io/desastre-ecuador/mapa.desastre.ec.svg?label=ready&title=Tareas%20listas%20para%20desarrollo)](http://waffle.io/desastre-ecuador/mapa.desastre.ec) 
[![Build Status](https://travis-ci.org/desastre-ecuador/mapa.desastre.ec.svg?branch=master)](https://travis-ci.org/desastre-ecuador/mapa.desastre.ec)

Este es un fork de Ushahidi v2, una plataforma para el mapeo de los informes sobre los daños ocurridos por el terremoto que afectó a las costas ecuatorianas el 16 de abril del 2016. La instancia en vivo se puede acceder en <https://mapa.desastre.ec/>.

## Cómo colaborar

Hay muchas formas:

- Reportando _bugs_ o sugiriendo mejoras a través de los [issues del repo en GitHub](https://github.com/desastre-ecuador/mapa.desastre.ec/issues).
- Resolviendo alguno de esos _issues_. Están priorizados y asignados en [este tablero de Waffle](https://waffle.io/desastre-ecuador/mapa.desastre.ec/join). Por favor, sigue [estas recomendaciones acerca de cómo contribuir](https://github.com/desastre-ecuador/mapa.desastre.ec/blob/master/CONTRIBUTING.md).
- Participando en la [lista de discusión de desarrollo](http://listas.desastre.ec/listinfo.cgi/desarrollo-desastre.ec).
- Participando o haciendo preguntas en el [chat del equipo técnico en Telegram](https://telegram.me/joinchat/AbmN-wcPvovTZcL0Lpr14Q).
- Moderando y verificando reportes. Más información en el [chat general en Telegram](https://telegram.me/joinchat/CV6MEghFJudTwP-hP64xVw).
- Ayudando en otras tareas menos técnicas, disponibles en [este tablero de Trello](https://trello.com/b/EVxI6km1).

## Cómo levantar el ambiente local

1. Instalar Git: http://git-scm.com/downloads (o GitHub para Windows si quieres una interfaz gráfica)
2. Instalar VirtualBox: https://www.virtualbox.org/wiki/Downloads
3. Instalar Vagrant: http://www.vagrantup.com/
4. Abrir una terminal
5. Clonar el proyecto: `git clone https://github.com/desastre-ecuador/mapa.desastre.ec`
6. Entrar al directorio del proyecto: `cd mapa.desastre.ec`
7. Iniciar vagrant: `vagrant up`

Luego, en tu navegador, ve a <http://localhost:8080> y sigue el proceso de instalación.

## Cómo levantar el ambiente local utilizando Docker Compose

1. Instalar Git: http://git-scm.com/downloads (o GitHub para Windows si quieres una interfaz gráfica)
2. Instalar Docker Compose: `https://docs.docker.com/compose/install/`
3. Abrir una terminal
4. Clonar el proyecto: `git clone https://github.com/desastre-ecuador/mapa.desastre.ec`
5. Entrar al directorio del proyecto: `cd mapa.desastre.ec`
6. Iniciar docker compose: `docker-compose up -d`

Luego, en tu navegador, ve a <http://localhost:8080> y sigue el proceso de instalación.

## Licencia

[LGPL versión 3](https://github.com/desastre-ecuador/mapa.desastre.ec/blob/master/License.txt).
