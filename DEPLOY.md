Información de como hacer deploy del Ushahidi
---------------------------------------------

El sistema de despliegue está configurado con [Capistano](http://capistranorb.com/),
tienes que [instalar la gema Ruby](http://capistranorb.com/documentation/getting-started/installation/)
antes de poder hacer deploys.

Una vez instalada la gema, si quieres hacer deploys a beta o a producción tienes
que pedir permisos en el canal [Ushahidi Ecuador Técnicos](https://telegram.me/joinchat/AbmN-wcPvovTZcL0Lpr14Q)


Hacer deploys a beta y a producción
-----------------------------------

Para hacer un deploy a beta, deberas ejecutar en el directorio root del proyecto:

    $ cap staging deploy

Para hacer un deploy a producción, deberas ejecutar:

    $ cap production deploy


Despliegues automáticos
-----------------------

Los _commits_ que llegan a [la rama `develop` del repositorio en GitHub](https://github.com/desastre-ecuador/mapa.desastre.ec/tree/develop)
son lanzados automáticamente al ambiente [beta](http://beta.mapa.desastre.ec) a través de
[Travis CI](https://travis-ci.org/desastre-ecuador/mapa.desastre.ec).
