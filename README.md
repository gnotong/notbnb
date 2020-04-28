# NOTBNB WEBSITE Example

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
  
