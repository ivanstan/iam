<?= $helper->getHeadPrintCode($entity_class_name.' index'); ?>

{% block body %}
    <div class="container-fluid mt-3 mx-3">
        <h1><?= $entity_class_name ?>s</h1>

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <a class="btn btn-primary" href="{{ path('<?= $route_name ?>_new') }}">New</a>
            </div>
        </div>

        <div class="row mb-3">
            <table class="table">
                <thead>
                    <tr>
<?php foreach ($entity_fields as $col => $field): ?>
                        <th><?= ucfirst($field['fieldName']) ?></th>
<?php endforeach; ?>
                        <th class="text-center" style="width: 120px">Action</th>
                    </tr>
                </thead>
                <tbody>
                {% for <?= $entity_twig_var_singular ?> in pager.currentPageResults %}
                    <tr>
<?php $i = 0 ?>
<?php foreach ($entity_fields as $col => $field): ?>
<?php if ($i === 0): ?>
                        <td>
                            <a href="{{ path('<?= $route_name ?>_show', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}">
                                {{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}
                            </a>
                        </td>
<?php else: ?>
                        <td class="d-none d-sm-table-cell">{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
<?php endif; ?>
<?php $i++ ?>
<?php endforeach; ?>
                        <td class="d-flex justify-content-center align-baseline">
                            <a href="{{ path('<?= $route_name ?>_edit', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}" class="btn btn-outline-primary mr-2 edit-button">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                            </a>
                            <div class="float-right">
                                {{ include('<?= $route_name ?>/_delete_form.html.twig') }}
                            </div>
                        </td>
                    </tr>
                {% else %}
                    <tr>
                        <td class="text-center" colspan="<?= (count($entity_fields) + 1) ?>">
                            no records found
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>

            {% include 'components/pager.html.twig' with {pager: pager} only %}
        </div>
    </div>
{% endblock %}
