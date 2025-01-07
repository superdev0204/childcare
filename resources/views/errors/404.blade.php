@extends('layouts.app')

@section('content')
    <h1>A 404 error occurred</h1>
    <h2><?= $exception->getMessage() ?></h2>

    <?php if ($exception instanceof NotFoundHttpException) :
        $reason = $exception->getMessage();
        switch ($reason) {
            case Application::ERROR_CONTROLLER_CANNOT_DISPATCH:
                $reasonMessage = 'The requested controller was unable to dispatch the request.';
                break;
            case Application::ERROR_MIDDLEWARE_CANNOT_DISPATCH:
                $reasonMessage = 'The requested middleware was unable to dispatch the request.';
                break;
            case Application::ERROR_CONTROLLER_NOT_FOUND:
                $reasonMessage = 'The requested controller could not be mapped to an existing controller class.';
                break;
            case Application::ERROR_CONTROLLER_INVALID:
                $reasonMessage = 'The requested controller was not dispatchable.';
                break;
            case Application::ERROR_ROUTER_NO_MATCH:
                $reasonMessage = 'The requested URL could not be matched by routing.';
                break;
            default:
                $reasonMessage = 'We cannot determine at this time why a 404 was generated.';
                break;
        }
    ?>
        <p><?= $reasonMessage ?></p>
    <?php endif ?>

    {{-- <?php if (isset($exception)
        && ($exception instanceof \Exception || $exception instanceof \Error)) : ?>
    <hr />

    <h2>Additional information:</h2>
    <h3><?= get_class($exception) ?></h3>
    <dl>
        <dt>File:</dt>
        <dd>
            <pre><?= $exception->getFile() ?>:<?= $exception->getLine() ?></pre>
        </dd>
        <dt>Message:</dt>
        <dd>
            <pre><?= $exception->getMessage() ?></pre>
        </dd>
        <dt>Stack trace:</dt>
        <dd>
            <pre><?= $exception->getTraceAsString() ?></pre>
        </dd>
    </dl>

    <?php if ($ex = $exception->getPrevious()) : ?>
    <hr />

    <h2>Previous exceptions:</h2>
    <ul class="list-unstyled">
        <?php $icount = 0; ?>
        <?php while ($ex) : ?>
        <li>
            <h3><?= get_class($ex) ?></h3>
            <dl>
                <dt>File:</dt>
                <dd>
                    <pre><?= $ex->getFile() ?>:<?= $ex->getLine() ?></pre>
                </dd>
                <dt>Message:</dt>
                <dd>
                    <pre><?= $ex->getMessage() ?></pre>
                </dd>
                <dt>Stack trace:</dt>
                <dd>
                    <pre><?= $ex->getTraceAsString() ?></pre>
                </dd>
            </dl>
        </li>
        <?php
        $ex = $ex->getPrevious();
        if (++$icount >= 50) {
            echo '<li>There may be more exceptions, but we do not have enough memory to process it.</li>';
            break;
        }
        ?>
        <?php endwhile ?>
    </ul>
    <?php endif ?>
    <?php else : ?>
    <h3>No Exception available</h3>
    <?php endif ?> --}}
@endsection
