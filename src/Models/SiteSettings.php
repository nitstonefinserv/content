<?php namespace Reflexions\Content\Models;

class SiteSettings extends \Eloquent {

    protected $fillable = ['key', 'data'];
    protected $casts = ['data' => 'object'];

    /**
     * Loads the siteSettings by key, then returns JSON parsed data
     *
     * @param string $key Key to lookup
     * @return \stdClass Object interpretation of the data
     * @throws \Exception
     */
    public static function getData($key) {
        $settings = self::where('key', $key)->first();

        if (!$settings) {
            throw new \Exception('Invalid key');
        }

        return $settings->data;
    }

    /**
     * @param string $key Key to lookup
     * @param \stdClass $value Data to store
     */
    public static function setData($key, $value) {
        $settings = self::where('key', $key)->first();

        if ($settings) {
            $settings->data = $value;
            $settings->save();
        } else {
            SiteSettings::create([
                'key' => $key,
                'data' => $value
            ]);
        }
    }

    public function getFormValue($key) {
        if ($key === 'key') {
            return $this->key;
        }

        return isset($this->data->$key) ? $this->data->$key : '';
    }

    public function save(array $options = []) {
        $data = $this->data;

        foreach ($this->data as $key => $value)
        {
            $data->$key = $this->$key;
            unset($this->$key);
        }

        $this->data = $data;

        parent::save($options);
    }
}
