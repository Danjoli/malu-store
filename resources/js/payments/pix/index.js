import { initPixCopy } from './copy';
import { initPixCountdown } from './countdown';
import { initPixStatus } from './status';

document.addEventListener(
    'DOMContentLoaded',
    () => {

        /*
        |--------------------------------------------------------------------------
        | Copiar Pix
        |--------------------------------------------------------------------------
        */

        initPixCopy();

        /*
        |--------------------------------------------------------------------------
        | Countdown
        |--------------------------------------------------------------------------
        */

        const countdown =
            initPixCountdown();

        /*
        |--------------------------------------------------------------------------
        | Status do pagamento
        |--------------------------------------------------------------------------
        */

        const status =
            initPixStatus({

                onPaid: () => {

                    countdown.stop();

                },

                onFailed: () => {

                    countdown.stop();

                }

            });

    }
);
