<?php
namespace outer\hbase;
use yii\base\Component;

/**
 */
class HbaseRow extends Component
{
    /**
     * @var Connection
     */
    public $db;

    public $table;
    
    public $key;

    /**
     * Deletes an entire row, a entire column family, or specific cell(s).
     *
     * Delete a entire row
     *    $hbase
     *        ->table('my_table')
     *            ->row('my_row')
     *                ->delete();
     *
     * Delete a entire column family
     *    $hbase
     *        ->table('my_table')
     *            ->row('my_row')
     *                ->delete('my_column_family');
     *
     * Delete all the cells in a column
     *    $hbase
     *        ->table('my_table')
     *            ->row('my_row')
     *                ->delete('my_column_family:my_column');
     *
     * Delete a specific cell
     *    $hbase
     *        ->table('my_table')
     *            ->row('my_row')
     *                ->delete('my_column_family:my_column','my_timestamp');
     */
    public function delete(){
        $args = func_get_args();
        $url = $this->table .'/'.$this->key;
        switch(count($args)){
            case 1;
                // Delete a column or a column family
                $url .= '/'.$args[0];
            case 2:
                // Delete a specific cell
                $url .= '/'.$args[1];
        }
        return $this->db->delete($url);
    }


    /**
     * Retrieve a value from a column row.
     *
     * Usage:
     *
     *    $hbase
     *        ->table('my_table')
     *            ->row('my_row')
     *                ->get('my_column_family:my_column');
     */
    public function get($column)
    {
        $getUrl = $this->table .'/'.$this->key.'/'.$column;

        $body = $this->db->get($getUrl);


        if(empty($body)){
            return null;
        }

        $arr = explode(':', $column);

        // ColumnFamily
        if (count($arr) == 1) {
            if (isset($body['Row'][0]['Cell'])) {
                $cells = $body['Row'][0]['Cell'];
                $ret = [];
                foreach ($cells as $cell) {
                    $c = base64_decode($cell['column']);
                    $columnName = str_replace($column.':', '', $c);
                    $ret[$columnName] = base64_decode($cell['$']);
                }
                return $ret;
            }   
        }
        
        // Column
        if (count($arr) == 2) {
            if (isset($body['Row'][0]['Cell'])) {
                return base64_decode($body['Row'][0]['Cell'][0]['$']);
            }
        }
        
        return null;
    }

    public function multiGet($column, $count = 100)
    {
        $body = $this->db->get($this->table .'/'.$this->key.'/'.$column . '/?v=' . $count);
        if(is_null($body)){
            return null;
        }
        return $this->parseMultiRowBody($body);
    }

    private function parseMultiRowBody($body)
    {
        $bodyArray = $body;

        $result = [];

        if (isset($bodyArray['Row'][0]['Cell']) && $bodyArray['Row'][0]['Cell'] > 0) {
            foreach ($bodyArray['Row'][0]['Cell'] as $key => $value) {
                $result[base64_decode($value['column'])] = [
                    'timestamp' => $value['timestamp'],
                    'data'      => base64_decode($value['$'])
                ];
            }
        }

        return $result;
    }

    /**
     * Create or update a column row.
     *
     * Usage:
     *    $db->tables()->table('')->row('')->put('my_column_family:my_column','my_value');
     *
     * Note, in HBase, creation and modification of a column value is the same concept.
     */
    public function put($column, $value, $timestamp=null){
        if (!isset($timestamp)) {
            $value = array(
                'Row' => array(array(
                    'key' => base64_encode($this->key),
                    'Cell' => array(array(
                        'column' => base64_encode($column),
                        '$' => base64_encode($value)
                    ))
                ))
            );
        }
        $this->db->put($this->table .'/'.$this->key.'/'.$column,$value,$timestamp);
        return $this;
    }


}