<?php

class DateType extends DateTime implements JsonSerializable {
    # Implement Json Serialize for DateType
    public function jsonSerialize() {
        return array(
            "__class__" => "date",
            "year" => $this->format("Y"),
            "month" => $this->format("m"),
            "day" => $this->format("d")
        );
    }
}

class DateTimeType extends DateTime implements JsonSerializable {
    # Implement Json Serialize for DateTimeType
    public function jsonSerialize() {
        return array(
            "__class__" => "datetime",
            "year" => $this->format("Y"),
            "month" => $this->format("m"),
            "day" => $this->format("d"),
            "hour" => $this->format("G"),
            "minute" => $this->format("i"),
            "second" => $this->format("s"),
            "microsecond" => $this->format("u")
        );
    }
}

class TimeType extends DateTime implements JsonSerializable {
    # Implement Json Serialize for TimeType
    public function jsonSerialize() {
        return array(
            "__class__" => "time",
            "hour" => $this->format("G"),
            "minute" => $this->format("i"),
            "second" => $this->format("s"),
            "microsecond" => $this->format("u")
        );
    }
}

class DecimalType implements JsonSerializable {
    # Implement Json Serialize for DecimalType
    public function __construct($value) {
        $this->_val = (float)$value;
    }
    public function jsonSerialize() {
        return array(
            "__class__" => "Decimal",
            "decimal" => $this->_val
        );
    }
}

class Fulfil {
    public static $apiKey = null;
    public static $instance = null;

    protected function getBaseUrl() {
        if (!self::$instance or !self::$apiKey) {
            trigger_error("Instance or Api Key is not configured.", E_USER_ERROR);
        }
        return "https://" . self::$instance . ".fulfil.io/api/v1";
    }

    protected function call($url, $method_type, $post_data=null) {
        # TODO: handle context
        $process = curl_init();
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "x-api-key: " . self::$apiKey,
            "Content-Length: " . strlen($post_data)
        ));

        curl_setopt($process, CURLOPT_URL, $url);
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, $method_type);
        if($post_data) {
            curl_setopt($process, CURLOPT_POST, 1);

            # Validate data before sending
            curl_setopt($process, CURLOPT_POSTFIELDS, $post_data);
        }

        $response = curl_exec($process);

        if( ! $response && $response != "") {
            trigger_error(curl_error($process), E_USER_ERROR);
        }

        $http_code = curl_getinfo($process, CURLINFO_HTTP_CODE);
        if(!(200 <= $http_code && $http_code < 300)) {
            # Throw error for non 2XX code
            trigger_error($http_code . ": " . $response, E_USER_ERROR);
        }

        curl_close($process);
        $json_response = json_decode($response, true);
        if ( ! is_null($json_response)) {
            return $json_response;
        }
        else {
            return $response;
        }
    }
}

class Model extends Fulfil {
    public function __construct($model) {
        $this->model = $model;
    }

    private function getUrl() {
        return $this->getBaseUrl() . "/model/" . $this->model;
    }

    public function create($params) {
        $data = json_encode($params);
        return $this->call($this->getUrl(), "POST", $data);
    }

    public function get($id) {
        $url = $this->getUrl() . "/" . $id;
        return $this->call($url, "GET");
    }

    public function search($filter, $fields=null, $page=1, $offset=0, $order=null, $perPage=null) {
        $params = array(
            "filter"=>json_encode($filter),
            "page"=>$page,
        );
        if($fields) {
            $params['field'] = $fields;
        }
        if($offset) {
            $params['offset'] = $offset;
        }
        if($order) {
            $params['order'] = $order;
        }
        if($perPage) {
            $params['per_page'] = $perPage;
        }
        $url = $this->getUrl() . '?' . http_build_query($params);

        // Replace field[0]=name&field[1]=company to field=name&field[1]=company
        $url = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $url);
        return $this->call($url, "GET");
    }

    public function delete($id) {
        $url = $this->getUrl() . "/" . $id;
        return $this->call($url, "DELETE");
    }

    public function run($method_name, $args, $id=null) {
        if ($id) {
            $url = $this->getUrl() . "/" . $id . "/" . $method_name;
        }
        else {
            $url = $this->getUrl() . "/" . $method_name;
        }
        $data = json_encode($args);
        return $this->call($url, "PUT", $data);
    }
}
