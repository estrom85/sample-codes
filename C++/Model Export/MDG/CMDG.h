#ifndef _CMDG_H_
#define _CMDG_H_

#include "../COLLADA/CCollada.h"
#include "../CVec3f.h"
#include <fstream>
#include <vector>
#include <string>
#include <GL/gl.h>
#include <GL/glu.h>


namespace mdg
{
class CMDG;
class CHeader;
class CVertexData;
class CFaceData;
class CMaterialData;
class CMaterials;
class CTextureData;
}


enum
{
    HEADER=0,
    VERTEX_DATA,
    FACE_DATA,
    TEXTURE_DATA,
    MATERIAL_DATA
};

enum
{
    HAS_TRIANGLES=0x00000001,
    HAS_QUADS=0x00000002,
    HAS_TEXTURE=0x00000004


};


/*******************************Material data class*************************************/
enum
{
    EMMISION = 0x000001,
    AMBIENT=0x000002,
    SPECULAR=0x000004,
    SHININESS=0x000008,
};

class CMaterials
{
public:
    char            id[20];
    unsigned int    flags;
    float           emmision[4];
    float           ambient[4];
    float           specular[4];
    float           shininess;

public:
    CMaterials();
    CMaterials(const CMaterials&);

    const CMaterials &operator=(const CMaterials &);

};



class CMaterialData
{
private:


private:
    unsigned int                numOfMaterials;
    std::vector<CMaterials>     materialData;

    unsigned int                materialDataSize;

public:
    CMaterialData();

public:
    CMaterials* getMaterial(char *id);

public:
    void loadMaterialData(CCollada *collada);

    void saveMaterialData(std::ofstream &file, unsigned int offset);
    void loadMaterialData(std::ifstream &file, unsigned int offset);

    unsigned int size();

};

/********************************Texture data class***********************************/

class CTextureData
{
private:
        char            filename[50];

    unsigned int        textureDataSize;

public:
    CTextureData();
    CTextureData(const CTextureData&);

    const CTextureData &operator=(const CTextureData&);

public:
    void loadTextureData(CCollada *collada);

    void saveTextureData(std::ofstream &file, unsigned int offset);
    void loadTextureData(std::ifstream &file, unsigned int offset);

    const char* getTexFilename();

    unsigned int size();


};



/**********************************Vertex data class*****************************************/

//trieda popisuje inform·cie o vertexoch: s˙radnice, norm·lne vektory a UV koordin·ty textury
enum
{
    POSITION_ARRAY=0,
    NORMALS_ARRAY,
    TEXCOORD_ARRAY
};



class CVertexData
{
private:
    unsigned int            numOfVertices;                  //number of vertices
    unsigned int            numOfNormals;                   //number of normals
    unsigned int            numOfTexCoords;                 //number of texture coordinates

    std::vector<CVec3f>     vertexData;
    std::vector<CVec3f>     normalsData;
    std::vector<CVec3f>     texCoordsData;

    unsigned int            vertexDataSize;

public:
    CVertexData();
    CVertexData(CVertexData&);

    const CVertexData &operator=(const CVertexData&);

public:
    void loadVertexData(CCollada *collada, unsigned int index);      //load vertex data from collada file

    void saveVertexData(std::ofstream &file, unsigned int offset);  //save vertex data into mdg file
    void loadVertexData(std::ifstream &file, unsigned int offset);  //load vertex data from mdg file

    unsigned int size();

    CVec3f &getVector(unsigned int index, unsigned int type);



};

/***********************************Face data class***************************************/

//trieda popisuje informacie o tv·rach, dok·ûe spracovaù len trojuholnÌky a ötvorce.
enum
{
    TRIANGLES=0,
    QUADS
};

class CFaceData
{

private:



    class CFaceDataArray
    {
    public:
        unsigned int        numOfFaces;                 //number of faces
        unsigned int        faceType;
        char                materialID[20];             //material ID
        CMaterials          *material;                  //pointer to material struct
        unsigned short      *positionsArray;            //array of vertex indexes
        unsigned short      *normalsArray;              //array of normals indexes
        unsigned short      *texCoordArray;             //array of texCoord indexes

        unsigned int        faceDataArraySize;

    public:
        CFaceDataArray(unsigned int faces, unsigned int type);
        CFaceDataArray(const CFaceDataArray&);
        ~CFaceDataArray();

        const CFaceDataArray &operator=(const CFaceDataArray&);
    };


private:
    unsigned int                numOfFaceArrays;                //number of triangle arrays
    std::vector<CFaceDataArray> faceDataArrays;                 //array of triangles arrays

    unsigned int        faceDataSize;

public:
    CFaceData();
    CFaceData(const CFaceData &);

    const CFaceData &operator=(const CFaceData&);

public:
    void connectMaterials(CMaterialData &materialData);

public:
    void loadFaceData(CCollada *collada, unsigned int index);       //load face data from collada file

    void saveFaceData(std::ofstream &file, unsigned int offset);    //save face data into mdg file
    void loadFaceData(std::ifstream &file, unsigned int offset);    //load face data from mdg file

    unsigned int size();

    void draw(CVertexData*,GLuint texid);


};

/**************************************Header class*******************************************/

//trieda popisuje z·kladnÈ inform·cie o meshi ako aj o s˙bore. identifikuje typ tv·rÌ,
//prepojenia, poËet prepojenÌ a z·kladnÈ vlastnosti meshu



class CHeader
{
private:
    char                id[4];                              //identification of file
    char                meshID[50];                         //identification of mesh
    unsigned int        flags;                              //description flags
    unsigned int        size;                               //size of file

    unsigned int        vertexDataOffset;                   //offset of vertex data in file
    unsigned int        faceDataOffset;                     //offset of face data in file
    unsigned int        textureDataOffset;                  //offset of texture data in file
    unsigned int        materialDataOffset;                 //offset of material data in file

    unsigned int        headerSize;

public:
    CHeader();

    CHeader(const CHeader &);
    const CHeader& operator=(const CHeader &);

public:
    void loadHeader(CCollada *collada, CVertexData &vetexData,
                    CFaceData &faceData, CTextureData &textureData,
                    CMaterialData &materialData,unsigned int index);               //load header info from collada file

    void saveHeader(std::ofstream &file, unsigned int offset);  //save header info into mdg file
    bool loadHeader(std::ifstream &file, unsigned int offset);  //load header info from mdg file

public:
    unsigned int getOffset(int dataType);

};




/************************************MoDel Geometry class******************************************/

//trieda popisuje z·kladn˙ geometriu statickÈho modelu, mÙûe obsahovaù maxim·lne 1 mesh,
//avöak je moûnÈ ku meshu pripojiù dcÈrske meshe, na ktorÈ sa bud˙ aplikovaù rovnakÈ transform·cie


class CMDG
{
private:
    CHeader             mHeader;                            //header data
    CVertexData         mVertexData;                        //vertex data
    CFaceData           mFaceData;                          //face data
    CTextureData        mTextureData;                       //texture data
    CMaterialData       mMaterialData;                      //material data

    std::string         path;
    GLuint              texID;

private:


    void setUpConnections();                                 //set up all connections




public:
    CMDG();

    void load(CCollada *collada, unsigned int index);   //import mesh from collada file

    void saveMDG(char* path);                           //save mesh geometry as mdg file
    void loadMDG(char* path);                           //load mesh geometry from mdg file

    void draw();

    void setPath(const char* path);







};

#endif // CMDG_H
