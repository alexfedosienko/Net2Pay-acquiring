# Net2Pay acquiring
Библиотека для работы с экваирингом от Net2Pay

### Возможности
 * Генерация формы для оплаты
 * Генерация массива данных для оплаты
 * Тестовый и боевой шлюзы Net2Pay

### Установка
С помощью [composer](https://getcomposer.org/):
```bash
composer require alexfedosienko/Net2Pay-acquiring
```
Подключение:
```php
use AFedosienko\NetPay;
```
## Примеры использования
### 1. Инициализация класса
```php
// $api_key - ключ API, выдается Net2Pay
// $auth_key - ключ авторизации, выдается Net2Pay
// $success_url - страница успешной оплаты
// $fail_url - страница не успешной оплаты
// $expire_days - количество дней, в течении которых действует оплата
// $dev - режим тестового шлюза
$netpay = new NetPay($api_key, $auth_key, $success_url, $fail_url, $expire_days, $dev);
```

### 2. Получаем данные для формы оплаты
```php
$result = $netpay->getPaymentData(
	1000, // сумма заказа в рублях
	'test_123', // номер заказа в система
	'Описание', // описание заказа
	'89000000000', // номер телефона, не обязательный параметр
	'mail@email.ru', // email, не обязательный параметр
);

// В результате будет массив 
$result['auth'];
$result['data'];
$result['expire'];
$result['url'];
```
Полученные данные подставляем в форму для оплаты

### 3. Получаем форму для оплаты
```php
$result = $netpay->getForm(
	1000, // сумма заказа в рублях
	'test_123', // номер заказа в система
	'Описание', // описание заказа
	'89000000000', // номер телефона, не обязательный параметр
	'mail@email.ru', // email, не обязательный параметр
)
```
В результате в переменной $result получим форму вида:
```html
<form action="url" method="POST">
	<input type="hidden" name="data" value="data">
	<input type="hidden" name="auth" value="auth">
	<input type="hidden" name="expire" value="expire">
	<input type="submit">
</form>
```
