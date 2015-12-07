#include "CMDG.h"
#include <cstring>
#include "../SOIL/SOIL.h"


/****************************CMDG class*****************************/

CMDG::CMDG()
{
    texID=0;


}

void CMDG::load(CCollada *collada, unsigned int index)
{
    mMaterialData.loadMaterialData(collada);
    mTextureData.loadTextureData(collada);
    mFaceData.loadFaceData(collada, index);
    mVertexData.loadVertexData(collada, index);
    mHeader.loadHeader(collada,mVertexData, mFaceData,mTextureData,mMaterialData,index);

    setUpConnections();
}

void CMDG::saveMDG(char *path)
{
    std::ofstream file;
    file.open(path,std::ios_base::out|std::ios_base::binary|std::ios_base::trunc);
    if (file.good())
    {
        mHeader.saveHeader(file, mHeader.getOffset(HEADER));
        mVertexData.saveVertexData(file, mHeader.getOffset(VERTEX_DATA));
        mFaceData.saveFaceData(file, mHeader.getOffset(FACE_DATA));
        mTextureData.saveTextureData(file, mHeader.getOffset(TEXTURE_DATA));
        mMaterialData.saveMaterialData(file, mHeader.getOffset(MATERIAL_DATA));

        file.close();
    }

}

void CMDG::loadMDG(char *path)
{
    std::ifstream file;
    file.open(path,std::ios_base::in|std::ios_base::binary);

    if(file.good())
    {
        if(mHeader.loadHeader(file,0))
        {
        mMaterialData.loadMaterialData(file,mHeader.getOffset(MATERIAL_DATA));
        mTextureData.loadTextureData(file,mHeader.getOffset(TEXTURE_DATA));
        mFaceData.loadFaceData(file,mHeader.getOffset(FACE_DATA));
        mVertexData.loadVertexData(file,mHeader.getOffset(VERTEX_DATA));
        }
    }
    setUpConnections();
}

void CMDG::setUpConnections()
{
   mFaceData.connectMaterials(mMaterialData);

}

void CMDG::draw()
{

    mFaceData.draw(&mVertexData,texID);
}

/********************************Header class**************************************/

CHeader::CHeader()
{
    strcpy(id,"MDG");
    meshID[0]='\0';
    flags=0;
    size=0;


    vertexDataOffset=78;
    faceDataOffset=78;
    textureDataOffset=78;
    materialDataOffset=78;

    headerSize=78;
}

CHeader::CHeader(const CHeader & other)
{
    strcpy(id,other.id);
    strcpy(meshID,other.meshID);
    flags=other.flags;
    size=other.size;

    vertexDataOffset=other.vertexDataOffset;
    faceDataOffset=other.faceDataOffset;
    textureDataOffset=other.textureDataOffset;
    materialDataOffset=other.materialDataOffset;

    headerSize=other.headerSize;

}

const CHeader &CHeader::operator =(const CHeader &other)
{
    if(this==&other) return *this;

    strcpy(id,other.id);
    strcpy(meshID,other.meshID);
    flags=other.flags;
    size=other.size;

    vertexDataOffset=other.vertexDataOffset;
    faceDataOffset=other.faceDataOffset;
    textureDataOffset=other.textureDataOffset;
    materialDataOffset=other.materialDataOffset;

    headerSize=other.headerSize;

    return *this;

}

void CHeader::loadHeader(CCollada *collada, CVertexData &vertexData,
                         CFaceData &faceData, CTextureData &textureData,
                         CMaterialData &materialData,unsigned int index)
{
    strcpy(meshID,collada->getGeometryID(index));

    vertexDataOffset=headerSize;
    faceDataOffset=vertexDataOffset+vertexData.size();
    textureDataOffset=faceDataOffset+faceData.size();
    materialDataOffset=textureDataOffset+textureData.size();

    size=materialDataOffset+materialData.size();

    if(collada->mGeometryLibrary.mGeometries[index].mMesh.mTriangles.size()!=0)
        flags+=HAS_TRIANGLES;
    if(collada->mGeometryLibrary.mGeometries[index].mMesh.mPolylist.size()!=0)
        flags+=HAS_QUADS;
    if(collada->mImageLibrary.mImages.size()!=0)
        flags+=HAS_TEXTURE;

}

void CHeader::saveHeader(std::ofstream &file, unsigned int offset)
{
    file.seekp(offset,std::ios_base::beg);

    file.write(id,sizeof(id));
    file.write((char*)&flags,sizeof(flags));
    file.write((char*)&size,sizeof(size));

    file.write((char*)&vertexDataOffset,sizeof(vertexDataOffset));
    file.write((char*)&faceDataOffset,sizeof(faceDataOffset));
    file.write((char*)&textureDataOffset,sizeof(textureDataOffset));
    file.write((char*)&materialDataOffset,sizeof(materialDataOffset));



}

bool CHeader::loadHeader(std::ifstream &file, unsigned int offset)
{
    file.seekg(offset,std::ios_base::beg);



    char temp[4];

    file.read(temp,sizeof(temp));

    if (strcmp(temp,id)!=0) return false;

        file.read((char*)&flags,sizeof(flags));
        file.read((char*)&size, sizeof(size));

        file.read((char*)&vertexDataOffset,sizeof(vertexDataOffset));
        file.read((char*)&faceDataOffset,sizeof(faceDataOffset));
        file.read((char*)&textureDataOffset,sizeof(textureDataOffset));
        file.read((char*)&materialDataOffset,sizeof(materialDataOffset));


    headerSize=vertexDataOffset;

    return true;
}

unsigned int CHeader::getOffset(int dataType)
{
    unsigned int temp;

    switch(dataType)
    {
    case HEADER:
        temp=0;
        break;

    case TEXTURE_DATA:
        temp=textureDataOffset;
        break;

    case VERTEX_DATA:
        temp=vertexDataOffset;
        break;

    case FACE_DATA:
        temp=faceDataOffset;
        break;

    case MATERIAL_DATA:
        temp=materialDataOffset;
        break;

    default:
        temp=0;
        break;
    }

    return temp;
}

void CMDG::setPath(const char *path)
{
    std::string temp=path;
    unsigned int locate=temp.find_last_of("/\\");
    this->path=temp.substr(0,locate+1);
    std::string filename=mTextureData.getTexFilename();
    std::string texfile=this->path+std::string(mTextureData.getTexFilename());

    texID=SOIL_load_OGL_texture(texfile.c_str(),SOIL_LOAD_AUTO,SOIL_CREATE_NEW_ID,SOIL_FLAG_POWER_OF_TWO);
}


