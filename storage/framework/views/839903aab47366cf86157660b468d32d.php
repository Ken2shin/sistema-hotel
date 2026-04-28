<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte: <?php echo e($report->name); ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            border-bottom: 2px solid #1e40af;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .logo {
            float: left;
            width: 150px;
        }
        .title-section {
            text-align: right;
        }
        .title-section h1 {
            color: #1e40af;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .meta-data {
            margin-top: 10px;
            font-size: 11px;
            color: #666;
        }
        .meta-data strong {
            color: #333;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 8px 12px;
            font-size: 14px;
            text-transform: uppercase;
            border-left: 4px solid #3b82f6;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    <div class="header">
        <div class="title-section">
            <h1>Hotel Management System</h1>
            <p>Reporte Confidencial</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="margin-bottom: 30px;">
        <h2 style="margin: 0; font-size: 18px;"><?php echo e($report->name); ?></h2>
        <div class="meta-data">
            <p><strong>Tipo de Análisis:</strong> <?php echo e(strtoupper($report->type)); ?></p>
            <p><strong>Período Auditado:</strong> <?php echo e($report->start_date?->format('d de M, Y')); ?> al <?php echo e($report->end_date?->format('d de M, Y')); ?></p>
            <p><strong>Generado por:</strong> <?php echo e($report->creator->name ?? 'Sistema'); ?> el <?php echo e($report->created_at?->format('d/m/Y H:i')); ?></p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($report->data && is_array($report->data)): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $report->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="section">
                <div class="section-title"><?php echo e(str_replace('_', ' ', $section)); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($values)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Métrica / Categoría</th>
                                <th class="text-right">Valor Registrado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($val)): ?>
                                    <tr>
                                        <td colspan="2" style="background-color: #f8fafc; font-weight: bold; padding-top: 15px;">
                                            <?php echo e(strtoupper(str_replace('_', ' ', $key))); ?>

                                        </td>
                                    </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $val; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subKey => $subVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td style="padding-left: 25px; color: #475569;"><?php echo e(str_replace('_', ' ', ucfirst($subKey))); ?></td>
                                            <td class="text-right font-bold">
                                                <?php echo e(is_numeric($subVal) ? number_format($subVal, 2) : $subVal); ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <tr>
                                        <td><?php echo e(str_replace('_', ' ', ucfirst($key))); ?></td>
                                        <td class="text-right font-bold">
                                            <?php echo e(is_numeric($val) ? number_format($val, 2) : $val); ?>

                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p><?php echo e($values); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        <div class="section">
            <p>No se encontraron registros cuantitativos para los parámetros de fechas y tipo seleccionados.</p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="footer">
        Documento generado automáticamente por el sistema. Válido sin firma. | Página <span class="page-number"></span>
    </div>

</body>
</html><?php /**PATH C:\Users\EMANUELCLEMENTEMARTI\Downloads\sistema hotel\resources\views/reports/pdf-template.blade.php ENDPATH**/ ?>