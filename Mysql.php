<?php

class Mysql
{
    protected $output_file;
    protected $tables;

    public function __construct()
    {
    }

    public function set_output_file($path)
    {
        $this->output_file = $path;
        return $this;
	}
	
	public function get_output_file()
	{
		return $this->output_file;
	}

    public function set_tables($tables)
    {
        $this->tables = $tables;
        return $this;
	}

    public function save_sql_file()
    {
		// write sql
		$wrap = "\r\n";
		
		$tables = $this->tables;

		$fp = fopen($this->output_file, 'w');

		fwrite($fp, "/*$wrap");
		fwrite($fp, " * Generated by DDLGen tool.$wrap");
		fwrite($fp, " */$wrap");
		fwrite($fp, $wrap);

		foreach ($tables as $name => $table) {
			fwrite($fp, "-- Table structure for table `$table->table_name`$wrap");
			fwrite($fp, "DROP TABLE IF EXISTS `$table->table_name`;$wrap");
			fwrite($fp, $wrap);
			fwrite($fp, "CREATE TABLE `$table->table_name` ( $wrap");

			foreach ($table->field as $f_idx => $field) {

				if ($f_idx !== 0) {
					fwrite($fp, "," . $wrap);
				}

				fwrite($fp, "\t`$field->field_name`");
				fwrite($fp, " $field->data_type");
				if ($field->not_null === 'Y') {
					fwrite($fp, " NOT NULL");
				}

				if ($field->default !== '') {
					fwrite($fp, " DEFAULT $field->default");
				}

				fwrite($fp, " $field->more");
				fwrite($fp, " COMMENT '$field->field_comments'");

			}

			if (is_array($table->pk)) {
				fwrite($fp, ",$wrap\tPRIMARY KEY (`" . implode('`,`', $table->pk) . "`)");
			}

			if (is_array($table->uk)) {
				ksort($table->uk);
				foreach ($table->uk as $uk_idx => $uk) {
					fwrite($fp, ",$wrap\tUNIQUE KEY `" . $table->table_name . "_uk$uk_idx` (`" . implode('`,`', $uk) . "`)");
				}
			}

			if (is_array($table->index)) {
				ksort($table->index);
				foreach ($table->index as $idx => $index) {
					fwrite($fp, ",$wrap\tINDEX `" . $table->table_name . "_index$idx` (`" . implode('`,`', $index) . "`)");
				}
			}
			fwrite($fp, "$wrap) COMMENT='$table->table_comments';$wrap");
			fwrite($fp, $wrap);
		}

		fclose($fp);

    }
}
