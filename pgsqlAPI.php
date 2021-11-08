<?php
    $paPDO = initDB();
    $paSRID = '4326';
    if(isset($_POST['functionname']))
    {
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoProvinceToAjax')
            $aResult = getGeoProvinceToAjax($paPDO, $paSRID, $paPoint);
        if ($functionname == 'getGeoDistricToAjax')
            $aResult = getGeoDistricToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getInfoRailToAjax')
            $aResult = getInfoRailToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getRailToAjax')
            $aResult = getRailToAjax($paPDO , $paSRID , $paPoint) ;
        else if ($functionname == 'getInfoWaterWayToAjax')
            $aResult = getInfoWaterWayToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getWaterWayToAjax')
            $aResult = getWaterWayToAjax($paPDO , $paSRID , $paPoint) ;
        else if ($functionname == 'getInfoRoadToAjax')
            $aResult = getInfoRoadToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getRoadToAjax')
            $aResult = getRoadToAjax($paPDO , $paSRID , $paPoint) ;
        
        echo $aResult;
    
        closeDB($paPDO);
    }

    // search-------------
    if (isset($_POST['name_search'])) {
        $name = $_POST['values_search'];
        $name_search = $_POST['name_search'];
        $aResult = "null";

        if ($name_search == 'getGeoProvinceToAjax') {
            $aResult = seachCityProvince($paPDO, $paSRID, $name);
        }
        if ($name_search == 'getGeoDistricToAjax') {
            $aResult = seachCityDistrict($paPDO, $paSRID, $name);
        }
        if ($name_search == 'getInforProvinceToAjax') {
            $aResult = searchInforProvinceToAjax($paPDO, $paSRID, $name);
        }
        if ($name_search == 'getInforDistrictToAjax') {
            $aResult = searchInforDistrictToAjax($paPDO, $paSRID, $name);
        }
        echo $aResult;
        closeDB($paPDO);

    }
   
    function initDB()
    {
        // Kết nối CSDL
        $paPDO = new PDO('pgsql:host=localhost;dbname=csdl;port=5432', 'postgres', '123456');
        return $paPDO;

    }
    function closeDB($paPDO)
    {
        // Ngắt kết nối
        $paPDO = null;
        // if ($paPDO == null) {
        //     print("Ngắt kết nối");
        // }
    }
    function query($paPDO, $paSQLStr)
    {
        try
        {
            // Khai báo exception
            $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Sử đụng Prepare 
            $stmt = $paPDO->prepare($paSQLStr);
            // Thực thi câu truy vấn
            $stmt->execute();
            
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            
            // Lấy danh sách kết quả
            $paResult = $stmt->fetchAll();   
            return $paResult;                 
        }
        catch(PDOException $e) {
            echo "Thất bại, Lỗi: " . $e->getMessage();
            return null;
        }       
    }
    function getGeoProvinceToAjax($paPDO,$paSRID,$paPoint)
    {
        
        $paPoint = str_replace(',', ' ', $paPoint);
       
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm36_tha_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
      
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }
    function getGeoDistricToAjax($paPDO,$paSRID,$paPoint)
    {
        
        $paPoint = str_replace(',', ' ', $paPoint);
       
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm36_tha_2\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
      
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }
    function seachCityProvince($paPDO,$paSRID,$name)
    {
        
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm36_tha_1\" where name_1 like '$name'";
        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            // Lặp kết quả
            foreach ($result as $item) {
                return $item['geo'];
            }
        } else
            return "null";
    }
    function seachCityDistrict($paPDO,$paSRID,$name)
    {
        
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm36_tha_2\" where name_2 like '$name'";
        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            // Lặp kết quả
            foreach ($result as $item) {
                return $item['geo'];
            }
        } else
            return "null";
    }
    function searchInforProvinceToAjax($paPDO, $paSRID, $name)
    {

        $name = str_replace(',', ' ', $name);
        
        $mySQLStr = "SELECT gid ,name_1,ST_Perimeter(gadm36_tha_1.geom), (ST_Area(gadm36_tha_1.geom)) from \"gadm36_tha_1\" where name_1 like '$name'";
       
        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>Gid: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tên thành phố : '.$item['name_1'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Chu vi: '.$item['st_perimeter'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Diện tích: '.$item['st_area'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }

    function searchInforDistrictToAjax($paPDO, $paSRID, $name)
    {

        $name = str_replace(',', ' ', $name);
        
        $mySQLStr = "SELECT gid ,name_2,ST_Perimeter(gadm36_tha_2.geom), (ST_Area(gadm36_tha_2.geom)) from \"gadm36_tha_2\" where name_2 like '$name'";
       
        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>Gid: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tên thành phố : '.$item['name_2'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Chu vi: '.$item['st_perimeter'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Diện tích: '.$item['st_area'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }
    // -------------------- Rail-----------------------------
    function getInfoRailToAjax($paPDO, $paSRID, $paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_railways_free_1\"  ";
        $mySQLStr = "SELECT gid ,name, ST_Length(geom::geometry) as length  from  \"gis_osm_railways_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";

        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item) {
                if($item['name'] == "") {
                    $item['name'] = "Không có tên";
                }
                $resFin = $resFin . '<tr><td>ID: ' . $item['gid'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Name: ' . $item['name'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Chiều dài: ' . $item['length'] . '</td></tr>';

                break;
            }
            $resFin = $resFin . '</table>';
            return $resFin;
        } else
            return "Bạn bấm quá xa!!";
    }
    function getRailToAjax($paPDO, $paSRID, $paPoint)
    {

    $paPoint = str_replace(',', ' ', $paPoint);

    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_railways_free_1\" ";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gis_osm_railways_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
    }

    // ----------------------Waterway----------------------
    
    function getInfoWaterWayToAjax($paPDO, $paSRID, $paPoint)
    {
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_waterways_free_1\" ";
    $mySQLStr = "SELECT gid,fclass,name , st_length(geom::geometry) as length  from \"gis_osm_waterways_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            if($item['name'] == "") {
                $item['name'] = "Không có tên";
            }
            $resFin = $resFin . '<tr><td>ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Loại Dòng: ' . $item['fclass'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Name: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Chiều dài: ' . $item['length'] . '</td></tr>';

            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "Bạn bấm quá xa!!!";
    }
    function getWaterWayToAjax($paPDO, $paSRID, $paPoint)
    {

    $paPoint = str_replace(',', ' ', $paPoint);

    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_waterways_free_1\" ";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gis_osm_waterways_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
    }

    // ---------------------------Road --------------------------------
    function getInfoRoadToAjax($paPDO, $paSRID, $paPoint)
    {
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_roads_free_1\" ";
    $mySQLStr = "SELECT gid,fclass,name , st_length(geom::geometry) as length  from \"gis_osm_roads_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            if($item['name'] == "") {
                $item['name'] = "Không có tên";
            }
            $resFin = $resFin . '<tr><td>ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Loại Đường: ' . $item['fclass'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Name: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Chiều dài: ' . $item['length'] . '</td></tr>';

            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "Bạn bấm quá xa!!!";
    }
    
    function getRoadToAjax($paPDO, $paSRID, $paPoint)
    {

    $paPoint = str_replace(',', ' ', $paPoint);

    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_roads_free_1\" ";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gis_osm_roads_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
    }
?>