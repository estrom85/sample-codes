#include "CMDG.h"
#include <sstream>

CVertexData::CVertexData()
{
    numOfVertices=0;
    numOfNormals=0;
    numOfTexCoords=0;

    vertexDataSize=0;

}

CVertexData::CVertexData(CVertexData &other)
{
    numOfVertices=other.numOfVertices;
    numOfNormals=other.numOfNormals;
    numOfTexCoords=other.numOfTexCoords;
    vertexDataSize=other.vertexDataSize;

    vertexData.clear();
    vertexData.insert(vertexData.begin(),other.vertexData.begin(),other.vertexData.end());

    normalsData.clear();
    normalsData.insert(normalsData.begin(),other.normalsData.begin(),other.normalsData.end());

    texCoordsData.clear();
    texCoordsData.insert(texCoordsData.begin(),other.texCoordsData.begin(),other.texCoordsData.end());
}

const CVertexData &CVertexData::operator =(const CVertexData &other)
{
    if (this==&other) return *this;

    numOfVertices=other.numOfVertices;
    numOfNormals=other.numOfNormals;
    numOfTexCoords=other.numOfTexCoords;
    vertexDataSize=other.vertexDataSize;

    vertexData.clear();
    vertexData.insert(vertexData.begin(),other.vertexData.begin(),other.vertexData.end());

    normalsData.clear();
    normalsData.insert(normalsData.begin(),other.normalsData.begin(),other.normalsData.end());

    texCoordsData.clear();
    texCoordsData.insert(texCoordsData.begin(),other.texCoordsData.begin(),other.texCoordsData.end());

}

void CVertexData::loadVertexData(CCollada *collada, unsigned int index)
{

    if(index<0||index>collada->mGeometryLibrary.mGeometries.size())
        index=0;

    CMesh* mesh = &collada->mGeometryLibrary.mGeometries[index].mMesh;

    CSource* vertexSource = collada->getVertexSource(mesh);
    CSource* normalsSource = 0;
    CSource* texCoordSource = 0;

    CFace* face;

    if(mesh->mPolylist.size()>0)
        face=&mesh->mPolylist[0];
    else if(mesh->mTriangles.size()>0)
            face=&mesh->mTriangles[0];
    else return;


    std::string normSourceID;
    std::string texSourceID;

    for (int i=0;i<face->mInputs.size();i++)
    {
        if(face->mInputs[i].mSemantic=="NORMAL")
        {
            normSourceID=face->mInputs[i].mSource;
        }
        if(face->mInputs[i].mSemantic=="TEXCOORD")
        {
            texSourceID=face->mInputs[i].mSource;
        }
    }

    normalsSource=collada->getSource(mesh,normSourceID);
    texCoordSource=collada->getSource(mesh,texSourceID);

    //vertex position data
    int XOffset;
    int YOffset;
    int ZOffset;


    numOfVertices=vertexSource->mTechniqueCommon.mAccessor.mCount;

    collada->setOffset(vertexSource,XOffset,YOffset,ZOffset);


    for (int i=0;i<numOfVertices;i++)
    {
        float x = collada->getFloatCoord(vertexSource,i,XOffset);
        float y = collada->getFloatCoord(vertexSource,i,YOffset);
        float z = collada->getFloatCoord(vertexSource,i,ZOffset);

        CVec3f temp(x,y,z);

        vertexData.push_back(temp);
    }

    //vertex normals data

    numOfNormals=normalsSource->mTechniqueCommon.mAccessor.mCount;

    collada->setOffset(normalsSource,XOffset,YOffset,ZOffset);

    for (int i=0;i<numOfNormals;i++)
    {
        float x = collada->getFloatCoord(normalsSource, i, XOffset);
        float y = collada->getFloatCoord(normalsSource,i,YOffset);
        float z = collada->getFloatCoord(normalsSource,i,ZOffset);

        CVec3f temp(x,y,z);

        normalsData.push_back(temp);
    }

    numOfTexCoords=texCoordSource->mTechniqueCommon.mAccessor.mCount;

    collada->setOffset(texCoordSource,XOffset,YOffset,ZOffset);


    //texture coordinates data
    for (int i=0;i<numOfTexCoords;i++)
    {
        float s = collada->getFloatCoord(texCoordSource,i,XOffset);
        float t = collada->getFloatCoord(texCoordSource,i,YOffset);

        CVec3f temp(s,t,0);

        texCoordsData.push_back(temp);
    }

    vertexDataSize=3*sizeof(unsigned int)+(3*sizeof(float))*(numOfVertices+numOfNormals+numOfTexCoords);


}

void CVertexData::saveVertexData(std::ofstream &file, unsigned int offset)
{
    file.seekp(offset,std::ios_base::beg);

    file.write((char*)&numOfVertices,sizeof(numOfVertices));
    file.write((char*)&numOfNormals, sizeof(numOfNormals));
    file.write((char*)&numOfTexCoords,sizeof (numOfTexCoords));

    for (int i=0;i<numOfVertices;i++)
    {
        float x=vertexData[i].x;
        float y=vertexData[i].y;
        float z=vertexData[i].z;

        file.write((char*)&x,sizeof(x));
        file.write((char*)&y,sizeof(y));
        file.write((char*)&z,sizeof(z));
    }

    for (int i=0;i<numOfNormals;i++)
    {
        float x=normalsData[i].x;
        float y=normalsData[i].y;
        float z=normalsData[i].z;

        file.write((char*)&x,sizeof(x));
        file.write((char*)&y,sizeof(y));
        file.write((char*)&z,sizeof(z));
    }

    for (int i=0;i<numOfTexCoords;i++)
    {
        float x=texCoordsData[i].x;
        float y=texCoordsData[i].y;
        float z=texCoordsData[i].z;


        file.write((char*)&x,sizeof(x));
        file.write((char*)&y,sizeof(y));
        file.write((char*)&z,sizeof(z));
    }


}



void CVertexData::loadVertexData(std::ifstream &file, unsigned int offset)
{
    file.seekg(offset,std::ios_base::beg);

    file.read((char*)&numOfVertices,sizeof(numOfVertices));
    file.read((char*)&numOfNormals,sizeof(numOfNormals));
    file.read((char*)&numOfTexCoords,sizeof(numOfTexCoords));

    for(int i=0;i<numOfVertices;i++)
    {
        CVec3f temp;

        file.read((char*)&temp.x,sizeof(temp.x));
        file.read((char*)&temp.y,sizeof(temp.y));
        file.read((char*)&temp.z,sizeof(temp.z));

        vertexData.push_back(temp);
    }

    for(int i=0;i<numOfNormals;i++)
    {
        CVec3f temp;

        file.read((char*)&temp.x,sizeof(temp.x));
        file.read((char*)&temp.y,sizeof(temp.y));
        file.read((char*)&temp.z,sizeof(temp.z));

        normalsData.push_back(temp);
    }

    for(int i=0;i<numOfTexCoords;i++)
    {
        CVec3f temp;

        file.read((char*)&temp.x,sizeof(temp.x));
        file.read((char*)&temp.y,sizeof(temp.y));
        file.read((char*)&temp.z,sizeof(temp.z));

        texCoordsData.push_back(temp);
    }


}



unsigned int CVertexData::size()
{
    return vertexDataSize;

}

CVec3f &CVertexData::getVector(unsigned int index, unsigned int type)
{
    switch(type)
    {
    case NORMALS_ARRAY:
        return normalsData[index];
        break;

    case TEXCOORD_ARRAY:
        return texCoordsData[index];
        break;

    case POSITION_ARRAY:
        return vertexData[index];
        break;
    }
}
