<?php
// generated Saturday 11th of April 2015 04:13:30 PM
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class chkVN extends be_module {
	public $searchname = 'Vietnam';
	public $searchlist = array(
			array( '001052000000', '001054240000' ),
			array( '001055000000', '001055192000' ),
			array( '001055208000', '001055240000' ),
			array( '014000016000', '014000032000' ),
			array( '014160000000', '014161064000' ),
			array( '014162000000', '014164000000' ),
			array( '014166000000', '014171000000' ),
			array( '027002000000', '027002096000' ),
			array( '027002128000', '027002160000' ),
			array( '027002192000', '027002224000' ),
			array( '027003000000', '027003080000' ),
			array( '027003096000', '027003128000' ),
			array( '027064000000', '027078128000' ),
			array( '042112000000', '042112160000' ),
			array( '042112176000', '042113208000' ),
			array( '042113224000', '042115000000' ),
			array( '042115048000', '042115080000' ),
			array( '042115096000', '042115240000' ),
			array( '042116032000', '042116048000' ),
			array( '042116064000', '042116080000' ),
			array( '042116112000', '042116128000' ),
			array( '042116144000', '042119128000' ),
			array( '042119160000', '042120000000' ),
			array( '058186016000', '058186208000' ),
			array( '058186224000', '058186240000' ),
			array( '058187000000', '058187192000' ),
			array( '058187224000', '058187240000' ),
			array( '101099000000', '101099064000' ),
			array( '103001208000', '103001212000' ),
			array( '103007036000', '103007040000' ),
			array( '103027060000', '103027068000' ),
			array( '103027231000', '103027231128' ),
			array( '103027236000', '103027240000' ),
			array( '103028036000', '103028040000' ),
			array( '103031124000', '103031128000' ),
			array( '103234088000', '103234092000' ),
			array( '103237064000', '103237068000' ),
			array( '103243104000', '103243108000' ),
			array( '103249020000', '103249024000' ),
			array( '103249100000', '103249104000' ),
			array( '103255236000', '103255240000' ),
			array( '112072064000', '112072128000' ),
			array( '112078000000', '112078016000' ),
			array( '112109088000', '112109096000' ),
			array( '112197000000', '112197160000' ),
			array( '112197176000', '112197184000' ),
			array( '112197192000', '112197224000' ),
			array( '113022000000', '113023128000' ),
			array( '113061108000', '113061112000' ),
			array( '113160000000', '113160192000' ),
			array( '113160224000', '113161032000' ),
			array( '113161064000', '113162000000' ),
			array( '113162032000', '113164032000' ),
			array( '113164064000', '113164096000' ),
			array( '113165000000', '113165096000' ),
			array( '113165128000', '113166064000' ),
			array( '113166128000', '113166224000' ),
			array( '113167032000', '113167128000' ),
			array( '113167160000', '113167192000' ),
			array( '113167224000', '113168032000' ),
			array( '113168064000', '113168096000' ),
			array( '113168128000', '113168160000' ),
			array( '113168192000', '113169032000' ),
			array( '113169160000', '113169192000' ),
			array( '113169224000', '113170000000' ),
			array( '113170064000', '113170128000' ),
			array( '113170160000', '113170192000' ),
			array( '113170224000', '113171000000' ),
			array( '113171032000', '113171064000' ),
			array( '113171128000', '113171192000' ),
			array( '113172000000', '113173064000' ),
			array( '113173096000', '113174000000' ),
			array( '113174160000', '113178048000' ),
			array( '113178080000', '113178096000' ),
			array( '113179000000', '113179016000' ),
			array( '113179032000', '113179064000' ),
			array( '113179080000', '113179096000' ),
			array( '113179240000', '113180000000' ),
			array( '113181000000', '113183000000' ),
			array( '113184000000', '113185032000' ),
			array( '113186000000', '113187032000' ),
			array( '113188000000', '113191000000' ),
			array( '116096000000', '116128000000' ),
			array( '116212032000', '116212064000' ),
			array( '118068000000', '118068176000' ),
			array( '118068192000', '118069208000' ),
			array( '118069224000', '118070032000' ),
			array( '118070064000', '118070208000' ),
			array( '118070224000', '118070240000' ),
			array( '118071000000', '118072000000' ),
			array( '119015160000', '119015192000' ),
			array( '120072096000', '120072104000' ),
			array( '120138072000', '120138073000' ),
			array( '123016000000', '123017000000' ),
			array( '123017096000', '123017128000' ),
			array( '123017176000', '123017224000' ),
			array( '123018000000', '123018032000' ),
			array( '123018064000', '123018160000' ),
			array( '123018192000', '123018224000' ),
			array( '123019032000', '123019048000' ),
			array( '123019080000', '123019112000' ),
			array( '123019208000', '123019240000' ),
			array( '123020000000', '123021016000' ),
			array( '123021032000', '123021048000' ),
			array( '123021080000', '123021096000' ),
			array( '123021160000', '123021176000' ),
			array( '123021192000', '123021240000' ),
			array( '123022064000', '123022096000' ),
			array( '123023016000', '123023032000' ),
			array( '123023064000', '123023080000' ),
			array( '123024000000', '123025032000' ),
			array( '123026000000', '123026096000' ),
			array( '123026160000', '123026224000' ),
			array( '123027000000', '123027032000' ),
			array( '123027064000', '123027160000' ),
			array( '123027224000', '123028000000' ),
			array( '123028064000', '123028096000' ),
			array( '123030000000', '123030016000' ),
			array( '123030048000', '123030080000' ),
			array( '123030128000', '123031000000' ),
			array( '125214000000', '125214064000' ),
			array( '125234000000', '125236000000' ),
			array( '125253112000', '125253128000' ),
			array( '171224000000', '172000000000' ),
			array( '180093000000', '180094000000' ),
			array( '180148000000', '180148008000' ),
			array( '180148128000', '180148144000' ),
			array( '182236112000', '182236116000' ),
			array( '182237020000', '182237024000' ),
			array( '183080000000', '183080048000' ),
			array( '183080064000', '183080080000' ),
			array( '183080096000', '183080112000' ),
			array( '183080128000', '183080160000' ),
			array( '183080176000', '183081128000' ),
			array( '183091000000', '183091016000' ),
			array( '183091026000', '183091032000' ),
			array( '202191056000', '202191060000' ),
			array( '203113128000', '203113192000' ),
			array( '203119008000', '203119012000' ),
			array( '203160000000', '203160002000' ),
			array( '203162000000', '203162002000' ),
			array( '203162016000', '203162032000' ),
			array( '203162080000', '203162096000' ),
			array( '203162112000', '203162144000' ),
			array( '203162160000', '203162192000' ),
			array( '203162224000', '203163000000' ),
			array( '203170026000', '203170028000' ),
			array( '203205000000', '203205064000' ),
			array( '203210192000', '203210208000' ),
			array( '210086232000', '210086240000' ),
			array( '210211096000', '210211128000' ),
			array( '210245000000', '210245016000' ),
			array( '210245020000', '210245024000' ),
			array( '210245031000', '210245032000' ),
			array( '210245048000', '210245064000' ),
			array( '210245080000', '210245096000' ),
			array( '220231064000', '220231128000' ),
			array( '222252000000', '222252192000' ),
			array( '222253096000', '222253128000' ),
			array( '222253160000', '222253192000' ),
			array( '222254000000', '222254064000' ),
			array( '222254096000', '222254192000' ),
			array( '222255000000', '222255032000' ),
			array( '222255096000', '223000000000' )
		);
}

?>