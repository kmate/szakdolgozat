<?php

namespace app\view;

use \fw\KeyValueStorage;
use \fw\input\DateValidator;
use \fw\input\EmailValidator;
use \fw\input\EnumValidator;
use \fw\input\RegexpValidator;
use \fw\input\StringCompareValidator;
use \fw\input\StringValidator;
use \fw\input\Validator;
use \fw\view\TemplateView;

class CustomTemplateView extends TemplateView
{
    public function getValidationResultRow(KeyValueStorage $data, $key, $additionalMessage = '')
    {
        if ($data->has($key))
        {
            $message = join('<br/>', $this->getValidatorMessage($data, $key, $additionalMessage));
            
            return '<tr class="validationError"><td colspan="2"><span>&uarr;</span>&nbsp;' . $message . '</td></tr>';
        }
        else
        {
            return '';
        }
    }
    
    public function getValidatorMessage(KeyValueStorage $data, $key, $additionalMessage = '')
    {
        $data     = $data->$key->toArray();
        $messages = array();
        
        foreach ($data as $errorType => $errorData)
        {
            switch ($errorType)
            {
                case DateValidator::ERROR_INVALID_DATE:
                    $messages[] = 'Érvényes dátumot kell megadni!';
                    break;
                
                case DateValidator::ERROR_BEFORE:
                    $messages[] = $errorData . ' előtti dátumot kell megadni!';
                    break;
                
                case DateValidator::ERROR_NOT_BEFORE:
                    $messages[] = 'Nem adható meg az alábi dátumnál korábbi: ' . $errorData;
                    break;
                
                case DateValidator::ERROR_AFTER:
                    $messages[] = $errorData . ' utáni dátumot kell megadni!';
                    break;
                
                case DateValidator::ERROR_NOT_AFTER:
                    $messages[] = 'Nem adható meg az alábi dátumnál későbbi: ' . $errorData;
                    break;
                
                case EmailValidator::ERROR_INVALID_ADDRESS:
                    $messages[] = 'Érvényes e-mail címet kell megadni!';
                    break;
                
                case EnumValidator::ERROR_INVALID_VALUE:
                    $messages[] = 'A megadott értékek közül válasszon egyet!';
                    break;
                
                case RegexpValidator::ERROR_NO_MATCH:
                    $messages[] = 'A beírt érték nem megfelelő formátumú!';
                    break;
                
                case StringCompareValidator::ERROR_NOT_EQUALS:
                    $messages[] = 'A két mező tartalma nem egyezik!';
                    break;
                
                case StringValidator::ERROR_MIN_LENGTH:
                    $messages[] = 'A minimális megadandó hossz: ' . $errorData . ' karakter!';
                    break;
                
                case StringValidator::ERROR_MAX_LENGTH:
                    $messages[] = 'A maximális megadható hossz: ' . $errorData . ' karakter!';
                    break;
                
                case Validator::ERROR_REQUIRED:
                    $messages[] = 'Ezt a mezőt kötelező kitölteni!';
                    break;
            }
        }
        
        if (!empty($additionalMessage))
        {
            $messages[] = $additionalMessage;
        }
        
        return $messages;
    }
}