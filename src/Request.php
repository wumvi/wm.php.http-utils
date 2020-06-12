<?php
declare(strict_types=1);

namespace Wumvi\Utils\Request;


/**
 * Получение переменных и работы с массивами GET или POST
 *
 * @author Козленко В.Л.
 */
class Request
{
    /**
     * Возвращает GET переменную
     *
     * @param string $name название переменной
     * @param string $default значение по умолчанию, если переменной нет
     *
     * @return string Значение переменной
     */
    public function get(string $name, string $default = ''): string
    {
        return $_GET[$name] ?? $default;
    }

    /**
     * Возвращает значение переменной из GET массива
     *
     * @param string $name название переменной
     * @param integer $default значение по умолчанию, если переменной нет или опеределена как пустая
     *
     * @return integer значение переменной
     */
    public function getInt(string $name, int $default = 0): int
    {
        $val = $_GET[$name] ?? $default;

        return $val === '' ? $default : (int)$val;
    }

    /**
     * Возвращает значение переменной из POST массива и преобразует в int
     *
     * @param string $name название переменной
     * @param integer $default значение по умолчанию, если переменной нет или опеределена как пустая
     *
     * @return integer значение переменной
     */
    public function postInt(string $name, int $default = 0): int
    {
        $val = $_POST[$name] ?? $default;

        return $val === '' ? $default : (int)$val;
    }

    /**
     * Возвращает POST переменную
     *
     * @param string $name Название параметра
     * @param string $default Значение по умолчанию, если переменной нет
     *
     * @return string Значение
     */
    public function post(string $name, string $default = ''): string
    {
        return $_POST[$name] ?? $default;
    }

    public function getPostRaw(): string
    {
        return file_get_contents('php://input');
    }

    /**
     * Возвращает TRUE, если запрос типа POST, иначе FALSE
     *
     * @return bool Post запрос это или нет
     */
    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Возвращает хост
     *
     * @return string хост
     */
    public function getHost(): string
    {
        return $_SERVER['HTTP_HOST'];
    }
}
