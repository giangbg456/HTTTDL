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
        else if ($functionname == 'getInfoProvinceToAjax')
            $aResult = getInfoProvinceToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getInfoDistrictToAjax')
            $aResult = getInfoDistrictToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getRailToAjax')
            $aResult = getRailToAjax($paPDO , $paSRID , $paPoint) ;
        else if ($functionname == 'getInfoWaterWayToAjax')
            $aResult = getInfoWaterWayToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getWaterWayToAjax')
            $aResult = getWaterWayToAjax($paPDO , $paSRID , $paPoint) ;
        else if ($functionname == 'getInfoWaterToAjax')
            $aResult = getInfoWaterToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getWaterToAjax')
            $aResult = getWaterToAjax($paPDO , $paSRID , $paPoint) ;
        else if ($functionname == 'getInfoLandUseToAjax')
            $aResult = getInfoLandUseToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getLandUseToAjax')
            $aResult = getLandUseToAjax($paPDO , $paSRID , $paPoint) ;
        else if ($functionname == 'getInfoPlaceToAjax')
            $aResult = getInfoPlaceToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getPlaceToAjax')
            $aResult = getPlaceToAjax($paPDO , $paSRID , $paPoint) ;
        else if ($functionname == 'getInfoNaturalToAjax')
            $aResult = getInfoNaturalToAjax($paPDO , $paSRID , $paPoint);
        else if ($functionname == 'getNaturalToAjax')
            $aResult = getNaturalToAjax($paPDO , $paSRID , $paPoint) ;
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


    function getInfoProvinceToAjax($paPDO,$paSRID,$paPoint)
    {
        
        $paPoint = str_replace(',', ' ', $paPoint);
        $mySQLStr = "SELECT name_1,ST_Perimeter(gadm36_tha_1.geom), (ST_Area(gadm36_tha_1.geom)) from \"gadm36_tha_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>Tên Thành phố: '.$item['name_1'].'</td></tr>';
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

    function getInfoDistrictToAjax($paPDO,$paSRID,$paPoint)
    {
        
        $paPoint = str_replace(',', ' ', $paPoint);
        $mySQLStr = "SELECT name_2,ST_Perimeter(gadm36_tha_2.geom), (ST_Area(gadm36_tha_2.geom)) from \"gadm36_tha_2\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>Tên Thành phố: '.$item['name_2'].'</td></tr>';
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

    // ---------------------------Water --------------------------------
        
    function getInfoWaterToAjax($paPDO, $paSRID, $paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_water_a_free_1\" ";
        $mySQLStr = "SELECT name,fclass, st_area(geom::geometry) as area , ST_Perimeter(geom::geometry) as perimeter from  \"gis_osm_water_a_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item) {
                if ($item['name'] == ""){
                    $item['name'] = "Không có tên";
                }
                $resFin = $resFin . '<tr><td>Tên: ' . $item['name'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Loại: ' . $item['fclass'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Diện tích: ' . $item['area'] . '</td></tr>';
                    $resFin = $resFin . '<tr><td>Chu vi: ' . $item['perimeter'] . '</td></tr>';

                break;
            }
            $resFin = $resFin . '</table>';
            return $resFin;
        } else
            return "null";
    }
    
    function getWaterToAjax($paPDO, $paSRID, $paPoint)
    {

    $paPoint = str_replace(',', ' ', $paPoint);

    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_water_a_free_1\"";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gis_osm_water_a_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
    }

// ---------------------------LandUse --------------------------------
        
    function getInfoLandUseToAjax($paPDO, $paSRID, $paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_landuse_a_free_1\" ";
        $mySQLStr = "SELECT name,fclass, st_area(geom::geometry) as area , ST_Perimeter(geom::geometry) as perimeter from  \"gis_osm_landuse_a_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item) {
                if ($item['name'] == ""){
                    $item['name'] = "Không có tên";
                }
                $resFin = $resFin . '<tr><td>Tên: ' . $item['name'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Loại: ' . $item['fclass'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Diện tích: ' . $item['area'] . '</td></tr>';
                    $resFin = $resFin . '<tr><td>Chu vi: ' . $item['perimeter'] . '</td></tr>';

                break;
            }
            $resFin = $resFin . '</table>';
            return $resFin;
        } else
            return "null";
    }

    function getLandUseToAjax($paPDO, $paSRID, $paPoint)
    {

    $paPoint = str_replace(',', ' ', $paPoint);

    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"gis_osm_landuse_a_free_1\"";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gis_osm_landuse_a_free_1\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
    }


    //--------------------Điểm--------------------------------
    function getInfoPlaceToAjax($paPDO, $paSRID, $paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"place_point\" ";
        $mySQLStr = "SELECT fclass,name, xcoord , ycoord from  \"place_point\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item) {
                if ($item['name'] == ""){
                    $item['name'] = "Không có tên";
                }
                $resFin = $resFin . '<tr><td>Tên: ' . $item['name'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Loại: ' . $item['fclass'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Kinh Độ: ' . $item['xcoord'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Vĩ Độ: ' . $item['ycoord'] . '</td></tr>';

                break;
            }
            $resFin = $resFin . '</table>';
            return $resFin;
        } else
            return "nulđâsl";
    }

    function getPlaceToAjax($paPDO, $paSRID, $paPoint)
    {

    $paPoint = str_replace(',', ' ', $paPoint);

    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"place_point\"";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"place_point\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
    }

    function getInfoNaturalToAjax($paPDO, $paSRID, $paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"natural_point\" ";
        $mySQLStr = "SELECT fclass,name, xcoord , ycoord from  \"natural_point\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item) {
                if ($item['name'] == ""){
                    $item['name'] = "Không có tên";
                }
                $resFin = $resFin . '<tr><td>Tên: ' . $item['name'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Loại: ' . $item['fclass'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Kinh Độ: ' . $item['xcoord'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Vĩ Độ: ' . $item['ycoord'] . '</td></tr>';

                break;
            }
            $resFin = $resFin . '</table>';
            return $resFin;
        } else
            return "nulđâsl";
    }

    function getNaturalToAjax($paPDO, $paSRID, $paPoint)
    {

    $paPoint = str_replace(',', ' ', $paPoint);

    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"natural_point\"";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"natural_point\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
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