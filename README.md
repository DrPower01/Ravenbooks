# Comment atteindre les objectifs de Ravenbooks ?

Vous vous demandez peut-être cela après avoir entendu que nous voulons mettre tous les livres de Djibouti sur notre site. Nous avons fait des essais et des erreurs, mais nous avons conclu que c'était une chose réalisable. Mais d'abord, laissez-moi vous expliquer comment nous avons progressé pour y parvenir.

La première chose que nous avons faite a été de prendre des photos des livres et de les entrer dans la base de données. Bien que cette méthode ait fonctionné correctement, elle semblait inhumaine et cela prenait beaucoup de temps. Nous avons donc fait des recherches et avons découvert l'ISBN, qui peut nous aider à identifier chaque livre individuellement.

Nous avons aussi découvert qu'il existait une API de Google appelée Google Books API, qui interagit avec la base de données de Google Books, et la réponse de l'API était au format JSON, ce qui était parfait pour nous. Nous avons créé un programme utilisant la bibliothèque Axios de Node.js pour récupérer les livres, puis les insérer dans notre base de données. Tout ce que nous devions faire était de récupérer l'ISBN de chaque bibliothèque et de les insérer un par un... mais cela ne semblait pas aussi efficace que je l'espérais.

Non seulement cela, mais les bibliothèques sont parfois réticentes à nous donner leurs ISBN, comme l'Université de Balballa, car elles pensent que leurs données doivent rester privées en raison de la réglementation scolaire, bien qu'elles nous aient donné le feu vert pour les collecter manuellement. Cela aurait pu être une bonne idée si les livres n'étaient pas en circulation. Mais aussi, le fait que les livres vieillissent, s'abîment et soient jetés fait que notre base de données serait obsolète et inutilisable avant qu'il ne soit trop tard.

Nous avons cherché un moyen de garder notre site à jour et avons découvert qu'il existe un service web couramment utilisé par certaines bibliothèques (PMB). Ce service web aide à organiser et à suivre les livres, et il fournit des API. Ce qui est bien avec ce service, c'est qu'il a été créé en PHP et est compatible avec ce que nous faisions.
