<?php

namespace Vsavritsky\PrerenderBundle;

final class Events
{
    // @codingStandardsIgnoreStart
    const shouldPrerenderPage = 'vsavritsky_prerender.should_prerender';
    const onBeforeRequest = 'vsavritsky_prerender.render.before';
    const onAfterRequest = 'vsavritsky_prerender.render.after';
    // @codingStandardsIgnoreEnd
}
