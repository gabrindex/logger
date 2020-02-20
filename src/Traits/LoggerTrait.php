<?php 

namespace gabrindex\logger\Traits;

date_default_timezone_set('Asia/Dubai');

use gabrindex\logger\Models\LoggerActivity;
use Illuminate\Support\Facades\Log;


trait LoggerTrait {

    public function store_log($instance_type, $instance_method, $reference_id, $log, $update = false, $edition_id = null, $event_id = null, $source_type = "internal")
    {   
        $status = true;

        if ($update) {
            if(is_bool($update)) {
                // Export fields
                $log = ["export_fields" => $log];
            } else {
                $log = $this->compare_updated_fields($log->toArray(), $update->toArray());                
            }
            if (count($log) > 0) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            if(is_array($reference_id)) {
                // Bulk update
                $reference_id = "," . implode(",", $reference_id) . ",";

                $log = ["bulk_update" => $log];
            } else {
                // Simple Log
                $log = ["log_message" => $log];
            }
            $status = true;
        }

        if ($source_type == "internal") {
            $created_by = \Auth::id();
        } else {
            $created_by = $reference_id;
        }

        if ($status) {
            try {
                LoggerActivity::create([
                    "instance_type" => $instance_type,
                    "instance_method" => $instance_method,
                    "reference_id" => $reference_id,
                    "log" => json_encode($log),
                    "created_by" => $created_by,
                    "source_type" => config("logger.source_types." . $source_type),
                    "edition_id" => $edition_id,
                    "event_id" => $event_id,
                    "created_at" => date("Y-m-d h:i:s"),
                    "mocked_by" => (filled(session('mocked_by'))) ? session('mocked_by') : null
                ]);


            } catch(\Exception $e) {
                dd($e->getMessage());
                Log::error('Exception in loggin', [$e]);
            }
            
        }
    }

    private function check_existance($instance_type, $instance_method, $reference_id)
    {
        $count = LoggerActivity::where("instance_type", $instance_type)
                        ->where("instance_method", $instance_method)
                        ->where("reference_id", $reference_id)
                        ->count();
        if ($count > 0) {
            // return false;
        }
        return true;
    }

    public function compare_updated_fields(array $new_data, array $old_data)
    {
        $unneeded_fields = ["created_at", "updated_at", "created_by", "updated_by"];
        $updated_fields = [];
        foreach ($old_data as $key => $old_value) {
            if ($old_value != $new_data[$key] && !in_array($key, $unneeded_fields)) {
                $updated_fields[$key]['old'] = $old_value;
                $updated_fields[$key]['new'] = $new_data[$key];
            }
        }
        return $updated_fields;
    }

    public function get_status_title($value) {
        if($value == 1) {
            return "Active";
        }
        return "Inactive";
    }

}