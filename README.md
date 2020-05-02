# NOTBNB WEBSITE Example

### Contains
- Développer une application Symfony complexe
- Maîtriser le concept de Controllers et de Routes paramétrées ou pas
- Maîtriser le langage de templating Twig et ses subtilités
- Utiliser le composant Form pour créer des formulaires riches et des sous-formulaires
- Utiliser des DataTransformers pour formater des données vers et depuis un formulaire
- Mettre en place des validations de formulaire
- Comprendre Doctrine et la liaison avec la base de données
- Construire une base de données pas à pas grâce aux entités et au système de migrations
- Créer des requêtes complexes grâce au DQL (Doctrine Query Language)
- Créer des jeux de fausses données grâce aux Fixtures
- Utiliser le composant Security pour sécuriser les pages, gérer les rôles et l'authentification des utilisateurs
- Personnaliser les pages d'erreurs les plus communes
- Comprendre la notion et l'utilité des Services
- Construire un système de pagination de A à Z
- Like feature on Announcements
- Adding Bootstrap Badge Extension

### Paginator

- Usage in a controller

```
#AdsController

public function index(int $page, Paginator $p) {
  $p->setEntityClass(Ad::class)->setCurrentPage($page);
  
  return $this->render('template_view.html.twig', ['paginator' => $p])
}
```

#template_view.html.twig

```
<table><thead>....</thead>
<tbody>
{% for ad in paginator.getData() %}
<tr>
<td>{{ ad.id}}</td><td>{{ ... }}</td>
</tr>
{% endfor %}
</tbody>
</table>
{{ paginator.render() }}
```

#service.yaml

```
services:
    ...
    
    App\service\Paginator:
        arguments:
            $templatePath: 'path_to/pagination.html.twig'
```

#pagination.html.twig

```
<div class="d-flex justify-content-center">
    <ul class="pagination">
        <li class="page-item {% if page == 1 %}disabled{% endif %}">
            <a class="page-link" href="{{ path(routeName, {page: page - 1}) }}">&laquo;</a>
        </li>
        {% for num in 1..pages %}
            <li class="page-item {% if num == page %} active {% endif %}">
                <a class="page-link" href="{{ path(routeName, {page: num}) }}">{{ num }}</a>
            </li>
        {% endfor %}
        <li class="page-item {% if page == pages %}disabled{% endif %}">
            <a class="page-link" href="{{ path(routeName, {page: page + 1}) }}">&raquo;</a>
        </li>
    </ul>
</div>
```
  
