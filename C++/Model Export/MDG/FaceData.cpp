#include "CMDG.h"
#include <sstream>

/***********************Face Data Array class**********************************/
CFaceData::CFaceDataArray::CFaceDataArray(unsigned int faces, unsigned int type)
{
    numOfFaces=faces;
    faceType=type;
    material=0;
    strcpy(materialID,"");
    faceDataArraySize=sizeof(numOfFaces)+sizeof(faceType)+
            sizeof(materialID);

    switch(faceType)
    {
    case TRIANGLES:
        positionsArray=new unsigned short [3*numOfFaces];
        normalsArray=new unsigned short [3*numOfFaces];
        texCoordArray=new unsigned short [3*numOfFaces];

        faceDataArraySize+=3*3*numOfFaces*sizeof(unsigned short);

        break;

    case QUADS:
        positionsArray=new unsigned short [4*numOfFaces];
        normalsArray=new unsigned short [4*numOfFaces];
        texCoordArray=new unsigned short [4*numOfFaces];

        faceDataArraySize+=3*4*numOfFaces*sizeof(unsigned short);

        break;
    }
}

CFaceData::CFaceDataArray::CFaceDataArray(const CFaceDataArray &other)
{
    numOfFaces=other.numOfFaces;
    faceType=other.faceType;
    strcpy(materialID,other.materialID);
    material=other.material;

    unsigned int arraySize;

    switch(faceType)
    {
    case TRIANGLES:
        positionsArray=new unsigned short [3*numOfFaces];
        normalsArray=new unsigned short [3*numOfFaces];
        texCoordArray=new unsigned short [3*numOfFaces];
        arraySize=3*numOfFaces;
        break;

    case QUADS:
        positionsArray=new unsigned short [4*numOfFaces];
        normalsArray=new unsigned short [4*numOfFaces];
        texCoordArray=new unsigned short [4*numOfFaces];
        arraySize=4*numOfFaces;
        break;
    }

    for(int i=0;i<arraySize;i++)
    {
        positionsArray[i]=other.positionsArray[i];
        normalsArray[i]=other.normalsArray[i];
        texCoordArray[i]=other.texCoordArray[i];
    }

    faceDataArraySize=other.faceDataArraySize;
}

CFaceData::CFaceDataArray::~CFaceDataArray()
{
    delete[] positionsArray;
    delete[] normalsArray;
    delete[] texCoordArray;

}

const CFaceData::CFaceDataArray &CFaceData::CFaceDataArray::operator =(const CFaceData::CFaceDataArray& other)
{
    if (this==&other) return *this;

    delete[] positionsArray;
    delete[] normalsArray;
    delete[] texCoordArray;

    numOfFaces=other.numOfFaces;
    faceType=other.faceType;
    strcpy(materialID,other.materialID);
    material=other.material;

    unsigned int arraySize;

    switch(faceType)
    {
    case TRIANGLES:
        positionsArray=new unsigned short [3*numOfFaces];
        normalsArray=new unsigned short [3*numOfFaces];
        texCoordArray=new unsigned short [3*numOfFaces];
        arraySize=3*numOfFaces;
        break;

    case QUADS:
        positionsArray=new unsigned short [4*numOfFaces];
        normalsArray=new unsigned short [4*numOfFaces];
        texCoordArray=new unsigned short [4*numOfFaces];
        arraySize=4*numOfFaces;
        break;
    }

    for(int i=0;i<arraySize;i++)
    {
        positionsArray[i]=other.positionsArray[i];
        normalsArray[i]=other.normalsArray[i];
        texCoordArray[i]=other.texCoordArray[i];
    }

    faceDataArraySize=other.faceDataArraySize;

    return *this;
}


/**************************Face Data class****************************************/

CFaceData::CFaceData()
{
    numOfFaceArrays=0;
    faceDataSize=sizeof(numOfFaceArrays);

}

CFaceData::CFaceData(const CFaceData &other)
{
    numOfFaceArrays=other.numOfFaceArrays;
    faceDataSize=other.faceDataSize;

    faceDataArrays.clear();
    faceDataArrays.insert(faceDataArrays.begin(),other.faceDataArrays.begin(),other.faceDataArrays.end());
}

const CFaceData &CFaceData::operator =(const CFaceData &other)
{
    if (this==&other) return *this;

    numOfFaceArrays=other.numOfFaceArrays;
    faceDataSize=other.faceDataSize;

    faceDataArrays.clear();
    faceDataArrays.insert(faceDataArrays.begin(),other.faceDataArrays.begin(),other.faceDataArrays.end());

    return *this;
}

void CFaceData::loadFaceData(CCollada *collada, unsigned int index)
{
    std::stringstream ss;
    CMesh *mesh = &collada->mGeometryLibrary.mGeometries[index].mMesh;

    unsigned numOfTriangles=mesh->mTriangles.size();
    unsigned numOfQuads=mesh->mPolylist.size();

    numOfFaceArrays=numOfTriangles+numOfQuads;

    for (int i=0;i<numOfTriangles;i++)
    {
        CTriangles *triangles = &mesh->mTriangles[i];
        unsigned int number=triangles->mCount;
        CFaceDataArray faces(number,TRIANGLES);
        strcpy(faces.materialID,triangles->mMaterial.c_str());

        int stride=triangles->mInputs.size();
        int PosOffset;
        int NormOffset;
        int TexOffset;

        for (int i=0;i<stride;i++)
        {
            if(triangles->mInputs[i].mSemantic=="VERTEX")
            {
                ss.clear();
                ss<<triangles->mInputs[i].mOffset;
                ss>>PosOffset;
            }
            else if(triangles->mInputs[i].mSemantic=="NORMAL")
            {
                ss.clear();
                ss<<triangles->mInputs[i].mOffset;
                ss>>NormOffset;
            }
            else if(triangles->mInputs[i].mSemantic=="TEXCOORD")
            {
                ss.clear();
                ss<<triangles->mInputs[i].mOffset;
                ss>>TexOffset;
            }
        }

        for (int i=0;i<faces.numOfFaces*3;i++)
        {
            faces.positionsArray[i]=triangles->mP.mIndices[i*stride+PosOffset];
            faces.normalsArray[i]=triangles->mP.mIndices[i*stride+NormOffset];
            faces.texCoordArray[i]=triangles->mP.mIndices[i*stride+TexOffset];
        }

        faceDataArrays.push_back(faces);
    }

    for(int i=0;i<numOfQuads;i++)
    {
        CPolylist *polylist = &mesh->mPolylist[i];
        unsigned int number=polylist->mCount;
        CFaceDataArray faces(number,QUADS);
        strcpy(faces.materialID,polylist->mMaterial.c_str());

        int stride=polylist->mInputs.size();
        int PosOffset;
        int NormOffset;
        int TexOffset;

        for (int i=0;i<stride;i++)
        {
            if(polylist->mInputs[i].mSemantic=="VERTEX")
            {
                ss.clear();
                ss<<polylist->mInputs[i].mOffset;
                ss>>PosOffset;
            }
            else if(polylist->mInputs[i].mSemantic=="NORMAL")
            {
                ss.clear();
                ss<<polylist->mInputs[i].mOffset;
                ss>>NormOffset;
            }
            else if(polylist->mInputs[i].mSemantic=="TEXCOORD")
            {
                ss.clear();
                ss<<polylist->mInputs[i].mOffset;
                ss>>TexOffset;
            }
        }

        for (int i=0;i<faces.numOfFaces*4;i++)
        {

            faces.positionsArray[i]=polylist->mP.mIndices[i*stride+PosOffset];
            faces.normalsArray[i]=polylist->mP.mIndices[i*stride+NormOffset];
            faces.texCoordArray[i]=polylist->mP.mIndices[i*stride+TexOffset];
            short temp1=faces.texCoordArray[i];
            short temp2=0;
        }

        faceDataArrays.push_back(faces);

    }

    faceDataSize=sizeof(numOfFaceArrays);
    for (int i=0;i<faceDataArrays.size();i++)
    {
        faceDataSize+=faceDataArrays[i].faceDataArraySize;
    }

}



void CFaceData::saveFaceData(std::ofstream &file, unsigned int offset)
{
    file.seekp(offset,std::ios_base::beg);

    file.write((char*)&numOfFaceArrays,sizeof(numOfFaceArrays));

    for (int i=0;i<numOfFaceArrays;i++)
    {
        CFaceDataArray *faces=&faceDataArrays[i];

        file.write((char*)&faces->numOfFaces,sizeof(faces->numOfFaces));
        file.write((char*)&faces->faceType,sizeof(faces->faceType));
        file.write(faces->materialID,sizeof(faces->materialID));

        unsigned int numOfVertices;
        if(faces->faceType==TRIANGLES) numOfVertices=3*faces->numOfFaces;
        else if(faces->faceType==QUADS) numOfVertices=4*faces->numOfFaces;
        else return;

        for(int i=0;i<numOfVertices;i++)
        {
            file.write((char*)&faces->positionsArray[i],sizeof(faces->positionsArray[i]));
            file.write((char*)&faces->normalsArray[i],sizeof(faces->normalsArray[i]));
            file.write((char*)&faces->texCoordArray[i],sizeof(faces->texCoordArray[i]));
        }
    }




}

void CFaceData::loadFaceData(std::ifstream &file, unsigned int offset)
{
    file.seekg(offset,std::ios_base::beg);

    file.read((char*)&numOfFaceArrays,sizeof(numOfFaceArrays));

    for (int i=0;i<numOfFaceArrays;i++)
    {
        unsigned int numOfFaces;
        unsigned int faceType;

        file.read((char*)&numOfFaces,sizeof(numOfFaces));
        file.read((char*)&faceType,sizeof(faceType));

        CFaceDataArray faces(numOfFaces,faceType);

        file.read(faces.materialID,sizeof(faces.materialID));

        unsigned int numOfVertices;
        if(faces.faceType==TRIANGLES) numOfVertices=3*faces.numOfFaces;
        else if(faces.faceType==QUADS) numOfVertices=4*faces.numOfFaces;
        else return;

        faces.positionsArray=new unsigned short[numOfVertices];
        faces.normalsArray=new unsigned short[numOfVertices];
        faces.texCoordArray=new unsigned short[numOfVertices];

        for (int i=0;i<numOfVertices;i++)
        {
            file.read((char*)&faces.positionsArray[i],sizeof(faces.positionsArray[i]));
            file.read((char*)&faces.normalsArray[i],sizeof(faces.normalsArray[i]));
            file.read((char*)&faces.texCoordArray[i],sizeof(faces.texCoordArray[i]));
        }

        faceDataArrays.push_back(faces);

    }

}

unsigned int CFaceData::size()
{
    return faceDataSize;
}

void CFaceData::connectMaterials(CMaterialData &materialData)
{

}

void CFaceData::draw(CVertexData *vertices,GLuint texid)
{
    glEnable(GL_TEXTURE_2D);
    glDisable(GL_TEXTURE_2D);
    glBindTexture(GL_TEXTURE_2D,texid);

    glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);
    glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);
    glScalef(5,5,5);

    for(int i=0;i<numOfFaceArrays;i++)
    {
        CFaceDataArray *faces=&faceDataArrays[i];
        unsigned int numOfVertices=0;

        if(faces->faceType==TRIANGLES)
        {
            glBegin(GL_TRIANGLES);
            numOfVertices=3*faces->numOfFaces;
        }
        else if(faces->faceType==QUADS)
        {
            glBegin(GL_QUADS);
            numOfVertices=4*faces->numOfFaces;
        }
        else return;

        for(int i=0;i<numOfVertices;i++)
        {

            CVec3f vert = vertices->getVector(faces->positionsArray[i],POSITION_ARRAY);
            CVec3f norm = vertices->getVector(faces->normalsArray[i],NORMALS_ARRAY);
            CVec3f tex = vertices->getVector(faces->texCoordArray[i],TEXCOORD_ARRAY);

            glTexCoord2f(tex.x,tex.y);
            glNormal3f(norm.x,norm.y,norm.z);
            glVertex3f(vert.x,vert.y,vert.z);
        }


        glEnd();
    }
}
