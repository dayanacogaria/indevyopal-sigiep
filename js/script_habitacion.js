var canvas = document.getElementById("renderCanvas");
var engine = new BABYLON.Engine(canvas, true);

var createScene = function () {
    var scene               = new BABYLON.Scene(engine);
    scene.clearColor        = BABYLON.Color3.Blue();
    scene.collisionsEnabled = true;

    var camera             = new BABYLON.FreeCamera("Camera", new BABYLON.Vector3(0, 30, -20), scene);
    camera.checkCollisions = true;
    camera.applyGravity    = true;
    camera.attachControl(canvas, true);
    camera.setTarget(new BABYLON.Vector3(0, 30, 0));

    var light = new BABYLON.DirectionalLight("dir02", new BABYLON.Vector3(0.2, -1, 0), scene);
    light.position = new BABYLON.Vector3(0, 80, 0);

    // Shadows
    var shadowGenerator = new BABYLON.ShadowGenerator(2048, light);

    // Physics
    scene.enablePhysics(null, new BABYLON.CannonJSPlugin());
    //scene.enablePhysics(null, new BABYLON.OimoJSPlugin());

    // Mesa de noche 1
    var table1                   = BABYLON.MeshBuilder.CreateBox("Table1", { height: 20, width: 20, depth: 20 }, scene);
    table1.position              = new BABYLON.Vector3(40, 5, 45);
    var materialTable            = new BABYLON.StandardMaterial("table1", scene);
    materialTable.diffuseTexture = new BABYLON.Texture("img/cama.jpg", scene);
    materialTable.emissiveColor  = new BABYLON.Color3(0.5, 0.5, 0.5);
    table1.material              = materialTable;
    table1.checkCollisions       = true;
    shadowGenerator.addShadowCaster(table1);

    // Cama
    var bed                      = BABYLON.MeshBuilder.CreateBox("Bed", { height: 15, width: 40, depth: 50 }, scene);
    bed.position                 = new BABYLON.Vector3(0, 3, 30);
    var materialBed              = new BABYLON.StandardMaterial("bed", scene);
    materialBed.diffuseTexture   = new BABYLON.Texture("img/cama.png", scene);
    materialBed.emissiveColor    = new BABYLON.Color3(0.5, 0.5, 0.5);
    // materialBed.diffuseTexture.hasAlpha = true;
    // materialBed.backFaceCulling = false;
    bed.material                 = materialBed;
    bed.checkCollisions          = true;
    shadowGenerator.addShadowCaster(bed);

    // Guarda Ropa
    var table2                    = BABYLON.MeshBuilder.CreateBox("Table2", { height: 47, width: 16, depth: 30 }, scene);
    table2.position               = new BABYLON.Vector3(-44, 19, 40);
    table2.checkCollisions        = true;
    table2.material               = materialTable;
    shadowGenerator.addShadowCaster(table2);

    // Mesa televisor
    var table3                    = BABYLON.MeshBuilder.CreateBox("Table3", { height: 30, width: 18, depth: 10 }, scene);
    var mtTable                   = new BABYLON.StandardMaterial('table3', scene);
    mtTable.diffuseTexture        = new BABYLON.Texture("img/cama.jpg", scene);
    mtTable.emissiveColor         = new BABYLON.Color3(0.5, 0.5, 0.5);
    table3.position               = new BABYLON.Vector3(0, 9, -45);
    table3.checkCollisions        = true;
    table3.material               = mtTable;
    shadowGenerator.addShadowCaster(table3);

    // Televisor
    var tv                        = BABYLON.MeshBuilder.CreateBox("tv", { height: 10, width: 5,}, scene);

    shadowGenerator.useBlurExponentialShadowMap = true;
    shadowGenerator.useKernelBlur               = true;
    shadowGenerator.blurKernel                  = 32;


    // Playground
    var ground             = BABYLON.Mesh.CreateBox("Ground", 1, scene);
    ground.scaling         = new BABYLON.Vector3(100, 1, 100);
    ground.position.y      = -5.0;
    ground.checkCollisions = true;

    var border0             = BABYLON.Mesh.CreateBox("border0", 1, scene);
    border0.scaling         = new BABYLON.Vector3(1, 100, 100);
    border0.position.y      = -5.0;
    border0.position.x      = -50.0;
    border0.checkCollisions = true;

    var border1             = BABYLON.Mesh.CreateBox("border1", 1, scene);
    border1.scaling         = new BABYLON.Vector3(1, 100, 100);
    border1.position.y      = -5.0;
    border1.position.x      = 50.0;
    border1.checkCollisions = true;

    var border2             = BABYLON.Mesh.CreateBox("border2", 1, scene);
    border2.scaling         = new BABYLON.Vector3(100, 100, 1);
    border2.position.y      = -5.0;
    border2.position.z      = 50.0;
    border2.checkCollisions = true;

    var border3             = BABYLON.Mesh.CreateBox("border3", 1, scene);
    border3.scaling         = new BABYLON.Vector3(100, 100, 1);
    border3.position.y      = -5.0;
    border3.position.z      = -50.0;
    border3.checkCollisions = true;

    var groundMat             = new BABYLON.StandardMaterial("groundMat", scene);
    groundMat.diffuseColor    = new BABYLON.Color3(0.5, 0.5, 0.5);
    groundMat.emissiveColor   = new BABYLON.Color3(0.2, 0.2, 0.2);
    groundMat.backFaceCulling = false;
    ground.material           = groundMat;
    border0.material          = groundMat;
    border1.material          = groundMat;
    border2.material          = groundMat;
    border3.material          = groundMat;
    ground.receiveShadows     = true;

    // Physics
    // box0.physicsImpostor = new BABYLON.PhysicsImpostor(box0, BABYLON.PhysicsImpostor.BoxImpostor, { mass: 2, friction: 0.4, restitution: 0.3 }, scene);
    // bed.physicsBody     = new BABYLON.PhysicsImpostor(bed, BABYLON.PhysicsImpostor.BoxImpostor, {mass: 100, friction: 0, restitution: 0}, scene);
    ground.physicsImpostor  = new BABYLON.PhysicsImpostor(ground, BABYLON.PhysicsImpostor.BoxImpostor, { mass: 0, friction: 0.5, restitution: 0.7 }, scene);
    border0.physicsImpostor = new BABYLON.PhysicsImpostor(border0, BABYLON.PhysicsImpostor.BoxImpostor, { mass: 0 }, scene);
    border1.physicsImpostor = new BABYLON.PhysicsImpostor(border1, BABYLON.PhysicsImpostor.BoxImpostor, { mass: 0 }, scene);
    border2.physicsImpostor = new BABYLON.PhysicsImpostor(border2, BABYLON.PhysicsImpostor.BoxImpostor, { mass: 0 }, scene);
    border3.physicsImpostor = new BABYLON.PhysicsImpostor(border3, BABYLON.PhysicsImpostor.BoxImpostor, { mass: 0 }, scene);

    // part0.physicsImpostor = new BABYLON.PhysicsImpostor(part0, BABYLON.PhysicsImpostor.BoxImpostor, { mass: 2, friction: 0.4, restitution: 0.3 }, scene);

    return scene;
}


var scene = createScene();

engine.runRenderLoop(function(){
    scene.render();
});

window.addEventListener("resize", function () {
   engine.resize();
});