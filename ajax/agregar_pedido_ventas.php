<html>
    <head>
    <!-- https://sweetalert2.github.io/ -->
    <script src="plugins/sweetalert/js/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="plugins/sweetalert/css/sweetalert2.css">
    <!-- https://sweetalert.js.org/guides/#installation -->
    <script src="plugins/sweetalert_2/js/sweetalert.js"></script>
    <script src="plugins/sweetalert_2/js/sweetalert.min.js"></script>
    </head>
</html>
<?php 
session_start();
$session_id = session_id();
if(isset($_POST['id'])){$id=$_POST['id'];}
if(isset($_POST['cantidad'])){$cantidad = $_POST['cantidad'];}
if(isset($_POST['precio_compra_'])){$precio_compra_ = $_POST['precio_compra_'];}
if(isset($_POST['iva'])){$iva = $_POST['iva'];}
require_once '../config/database.php';//********************************requiere BD
// *************************************** insert ********************************
if(!empty($id) and !empty($cantidad) and !empty($precio_compra_)){
    $insert_tmp = mysqli_query($mysqli, "INSERT INTO tmp (id_producto, cantidad_tmp, precio_tmp, session_id)VALUES('$id', '$cantidad', '$precio_compra_','$session_id');");
}
// **************************************** delete ********************************
if(isset($_GET['id'])){
    $id=intval($_GET['id']);
    $delete=mysqli_query($mysqli, "DELETE FROM tmp WHERE id_tmp='".$id."';");
}
?>
<table class="table table-bordered table-striped table-hover">
    <tr class="warning">
        <th>Código</th>
        <th>Tipo de Produ.</th>
        <th>Unid. de medida</th>
        <th>Producto</th>
        <th><span class="pull-right">Cantidad</span></th>
        <th><span class="pull-right">Precio</span></th>
        <th><span class="pull-right">Iva</span></th>
        <th style="width: 36px;"></th>
    </tr>
    <?php 
        $suma_total=0;
        $sql=mysqli_query($mysqli, "SELECT * FROM producto, tmp WHERE producto.cod_producto=tmp.id_producto and tmp.session_id='".$session_id."';");
        while($row=mysqli_fetch_array($sql)){
            $id_tmp=$row['id_tmp'];
            $codigo_producto=$row['cod_producto'];
            $descrip_producto=$row['p_descrip'];
            $cantidad=$row['cantidad_tmp'];
            $codigo_tproducto=$row['cod_tipo_prod'];
            $sql_tproducto = mysqli_query($mysqli, "SELECT t_p_descrip FROM tipo_producto WHERE cod_tipo_prod='$codigo_tproducto';");
            $rw_tproducto = mysqli_fetch_array($sql_tproducto);
            $tproducto_nombre= $rw_tproducto['t_p_descrip'];
            $id_u_medida=$row['id_u_medida'];
            $sql_umedida = mysqli_query($mysqli, "SELECT u_descrip FROM u_medida WHERE id_u_medida='$id_u_medida';");
            $rw_u_medida = mysqli_fetch_array($sql_umedida);
            $u_medida_nombre= $rw_u_medida['u_descrip'];
            $precio_compra_=$row['precio_tmp'];
            // ***************************calculo de las operaciones.***************************************
            $precio_compra_f=number_format($precio_compra_); //***********************Format para para dividir en decimales en ,
            $precio_compra_r=str_replace(",","",$precio_compra_f);//******************remplace las comas si existe.
            $precio_total=$precio_compra_r*$cantidad;//*******************************multiplica el total
            $precio_total_f=number_format($precio_total);//***************************Format para para dividir en decimales en ,
            $precio_total_r=str_replace(",","",$precio_total_f);
            $suma_total+=$precio_total_r; //*****************************************suma total

            $ivaCadaUno = number_format($precio_total  * $iva / 100);
            $ivaCadaUno10 = ($precio_total  * $iva / 100);
            $ivaTotal+=$ivaCadaUno10;
            ?>
            <tr>
                <td><?php echo $codigo_producto; ?></td>
                <td><?php echo $tproducto_nombre; ?></td>
                <td><?php echo $u_medida_nombre; ?></td>
                <td><?php echo $descrip_producto; ?></td>
                <td><span class="pull-right"><?php echo $cantidad; ?></span></td>
                <td><span class="pull-right"><?php echo $precio_total_f; ?></span></td>
                <td><span class="pull-right"><?php echo $ivaCadaUno; ?></span></td>
                <td><span class="pull-right" title="Eliminar de la lista"><a href="#" onclick="mensajeEliminar(); eliminar('<?php echo $id_tmp; ?>');"><i class="glyphicon glyphicon-trash"></i></a></span></td>
            </tr>
       <?php } ?>
            <tr>
                <input type="hidden" class="form-control" name="suma_total" value="<?php echo $suma_total; ?>">
                <?php if(empty($codigo_producto)){
                    $codigo_producto=0;
                }else {
                    $codigo_producto;} ?>
                <input type="hidden" class="form-control" name="codigo_producto" value="<?php echo $codigo_producto; ?>">
                <?php if(empty($cantidad)){
                    $cantidad=0;
                }else {
                    $cantidad;} ?>
                <input type="hidden" class="form-control" name="cantidad" value="<?php echo $cantidad; ?>">
                <td colspan=5><b><span class="pull-right">Total Gs.</span></b></td>
                <td>
                    <b>
                        <span class="pull-right"><?php echo number_format($suma_total); ?></span>
                    </b>
                </td>
                <td>
                    <b>
                        <span class="pull-right"><?php echo number_format($ivaTotal); ?></span>
                    </b>
                </td>
            </tr>
</table>
<script>
function mensajeEliminar(){
swal({
    title: "Producto",
    text: "Eliminado...!!!",
    position: 'top-center',
    icon: 'success',
    timer: 1000
});
}
</script>